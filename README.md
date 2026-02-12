## Démarrage Rapide

### Prérequis
- PHP 8.2+
- Composer
- Node.js 20+
- MySQL 8.0+

### Installation 

```bash
# 1. Backend
cd backend
composer install
cp .env.example .env.local
# Éditez .env.local avec vos infos MySQL

php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

# 2. Frontend
cd ../frontend
npm install
```

---

## Lancer l'Application

### Option 1 : Développement (recommandé)

**Terminal 1 - Backend :**
```bash
cd backend
symfony serve
```

**Terminal 2 - Frontend :**
```bash
cd frontend
npm run dev
```


### Option 2 : Mode Production (test local)

```powershell
cd frontend
npm run build

# Lancer
cd backend
symfony serve
```

---

## 🧪 Tests

```bash
# Backend
cd backend
php bin/phpunit
vendor/bin/behat

# Frontend
cd frontend
npm run lint
npm run cypress:run
```

---

## Déploiement (Alwaysdata)

### Configuration (une seule fois)

1. **Ajoutez les secrets GitHub** (Settings → Secrets → Actions) :
   - `ALWAYSDATA_SSH_KEY` : Votre clé privée SSH
   - `ALWAYSDATA_USER` : Nom d'utilisateur
   - `ALWAYSDATA_ACCOUNT` : Nom du compte
   - `ALWAYSDATA_PATH` : Chemin (ex: `/home/user/www`)

   📖 Détails : [.github/SECRETS_GUIDE.md](.github/SECRETS_GUIDE.md)

2. **Créez le site sur Alwaysdata** :
   - Type : PHP 8.2+
   - Racine : `/home/votre-user/www/backend/public`

3. **Créez `.env.local` sur le serveur** :
   ```bash
   ssh user@ssh-compte.alwaysdata.net
   cd www/backend
   nano .env.local
   ```
   ```env
   APP_ENV=prod
   APP_SECRET=votre-secret
   DATABASE_URL="mysql://user:pass@127.0.0.1:3306/db?serverVersion=8.0"
   ```

### Déployer

```bash
git push origin main
```

---

## Structure

```
ugselweb/
├── backend/           # API Symfony
│   ├── src/
│   │   ├── Controller/    # Endpoints API
│   │   └── Entity/        # Modèles de données
│   └── public/
│       └── app/           # Frontend buildé (prod)
├── frontend/          # Application React
│   ├── src/
│   │   ├── components/    # Sports, Competitions, Épreuves
│   │   └── api.ts         # Client API
│   └── cypress/           # Tests E2E
└── .github/
    └── workflows/         # CI/CD automatique
```