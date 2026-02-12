# Architecture de Production

## 🏗️ Vue d'ensemble

En production, le frontend React est intégré directement dans le backend Symfony :

```
votre-domaine.alwaysdata.net
│
├── /api/*           → API REST Symfony
├── /app/*           → Assets statiques du frontend (JS, CSS, images)
└── /*               → Application React (SPA)
```

## 📦 Comment ça fonctionne ?

### 1. Build du Frontend

Le frontend React est compilé en fichiers statiques :
```bash
cd frontend
npm run build
# Génère frontend/dist/ avec index.html, JS, CSS, etc.
```

### 2. Intégration dans le Backend

Les fichiers buildés sont copiés dans `backend/public/app/` :
```
backend/public/
├── index.php          # Point d'entrée Symfony
├── .htaccess          # Règles de routing
└── app/               # Frontend React buildé
    ├── index.html
    ├── assets/
    │   ├── index-xxx.js
    │   └── index-xxx.css
    └── vite.svg
```

### 3. Routing

Le fichier `.htaccess` gère le routing :
- `/api/*` → traité par Symfony (API REST)
- `/app/*` → fichiers statiques servis directement
- Toutes les autres routes → `FrontendController` qui sert `index.html`

### 4. Frontend Controller

```php
// src/Controller/FrontendController.php
#[Route('/{route}', requirements: ['route' => '^(?!api).*'], priority: -1)]
public function index(): Response
{
    return new Response(file_get_contents('public/app/index.html'));
}
```

Ce contrôleur :
- Capture toutes les routes sauf `/api/*`
- Sert toujours `index.html` → React Router prend le relais côté client
- A la priorité la plus basse pour ne pas interférer avec l'API

## 🚀 Déploiement Automatique

Le workflow GitHub Actions fait automatiquement :

```yaml
1. Build frontend → frontend/dist/
2. Copie dans backend/public/app/
3. Déploiement rsync vers Alwaysdata
4. Backend sert le frontend + API
```

## 🧪 Test en Local

### Option 1 : Mode Développement (recommandé)

Deux serveurs séparés :

```bash
# Terminal 1 - Backend (API)
cd backend
symfony serve
# → http://localhost:8000

# Terminal 2 - Frontend (avec hot reload)
cd frontend  
npm run dev
# → http://localhost:5173
```

Le frontend sur le port 5173 proxy les requêtes `/api/*` vers le port 8000.

### Option 2 : Mode Production en Local

Un seul serveur (comme en production) :

```bash
# Build et intégration
./build-frontend.ps1  # Windows
# ou
./build-frontend.sh   # Linux/Mac

# Lancer le backend
cd backend
symfony serve
# → http://localhost:8000 (frontend + API)
```

## 🌐 Configuration API

Le frontend détecte automatiquement l'URL de l'API :

```typescript
// frontend/src/api.ts
const API_URL = import.meta.env.VITE_API_URL || 
  (window.location.hostname === 'localhost' 
    ? 'http://localhost:8000/api'      // Dev
    : `${window.location.origin}/api`); // Production
```

- **En développement** : `http://localhost:8000/api`
- **En production** : `https://votre-site.alwaysdata.net/api`

## 📋 Configuration Alwaysdata

### Site Web

Créez un site dans l'interface Alwaysdata :

1. **Sites** → **Ajouter un site**
2. **Type** : PHP
3. **Racine** : `/home/votre-user/www/backend/public` ⚠️ Important !
4. **Version PHP** : 8.2+
5. **Domaine** : `votre-compte.alwaysdata.net` ou votre domaine personnalisé

### Variables d'environnement

Créez `backend/.env.local` sur le serveur :

```env
APP_ENV=prod
APP_SECRET=votre-secret-genere
DATABASE_URL="mysql://user:pass@127.0.0.1:3306/dbname?serverVersion=8.0"
CORS_ALLOW_ORIGIN='^https?://(.*\.)?alwaysdata\.net$'
```

## 🔧 Avantages de cette Architecture

✅ **Un seul domaine** : Pas de problèmes CORS  
✅ **Déploiement simple** : Un seul site à configurer  
✅ **Performance** : Fichiers statiques servis directement par Apache  
✅ **SEO** : Possibilité d'ajouter du SSR plus tard  
✅ **Sécurité** : API et frontend partagent la même origine  

## 🐛 Dépannage

### Le frontend ne charge pas

Vérifiez :
```bash
ssh votre-user@ssh-votre-compte.alwaysdata.net
ls -la www/backend/public/app/
# Doit contenir : index.html, assets/, etc.
```

### Erreurs 404 sur les routes React

Le `.htaccess` doit rediriger toutes les routes vers `index.php`.  
Vérifiez que `mod_rewrite` est activé sur Alwaysdata (généralement oui par défaut).

### L'API ne répond pas

Testez directement l'API :
```bash
curl https://votre-site.alwaysdata.net/api/sports
```

### Cache navigateur

Après un déploiement, forcez le rafraîchissement : `Ctrl + F5`

## 📚 Références

- [Symfony Production Best Practices](https://symfony.com/doc/current/deployment.html)
- [Vite Build Production](https://vitejs.dev/guide/build.html)
- [React Router sur Serveur Apache](https://create-react-app.dev/docs/deployment/#serving-apps-with-client-side-routing)
