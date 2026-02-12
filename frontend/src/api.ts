const API_URL = 'http://localhost:8000/api';

export interface Sport {
  id: number;
  nom: string;
  type: 'individuel' | 'collectif';
  championnatsCount?: number;
  championnats?: Championnat[];
}

export interface Championnat {
  id: number;
  nom: string;
  sport?: { id: number; nom: string };
  competitionsCount?: number;
  competitions?: Competition[];
}

export interface Competition {
  id: number;
  nom: string;
  championnat?: { 
    id: number; 
    nom: string;
    sport?: {
      id: number;
      nom: string;
    };
  };
  epreuvesCount?: number;
  epreuves?: Epreuve[];
}

export interface Epreuve {
  id: number;
  nom: string;
  competition?: { id: number; nom: string };
}

// Sports API
export const sportsApi = {
  getAll: async (): Promise<Sport[]> => {
    const response = await fetch(`${API_URL}/sports`);
    return response.json();
  },
  
  getOne: async (id: number): Promise<Sport> => {
    const response = await fetch(`${API_URL}/sports/${id}`);
    return response.json();
  },
  
  create: async (data: { nom: string; type: 'individuel' | 'collectif' }): Promise<Sport> => {
    const response = await fetch(`${API_URL}/sports`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data)
    });
    return response.json();
  },
  
  delete: async (id: number): Promise<void> => {
    await fetch(`${API_URL}/sports/${id}`, { method: 'DELETE' });
  }
};

// Championnats API
export const championnatsApi = {
  getAll: async (): Promise<Championnat[]> => {
    const response = await fetch(`${API_URL}/championnats`);
    return response.json();
  },
  
  getOne: async (id: number): Promise<Championnat> => {
    const response = await fetch(`${API_URL}/championnats/${id}`);
    return response.json();
  },
  
  create: async (data: { nom: string; sportId: number }): Promise<Championnat> => {
    const response = await fetch(`${API_URL}/championnats`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data)
    });
    return response.json();
  },
  
  delete: async (id: number): Promise<void> => {
    await fetch(`${API_URL}/championnats/${id}`, { method: 'DELETE' });
  }
};

// Competitions API
export const competitionsApi = {
  getAll: async (): Promise<Competition[]> => {
    const response = await fetch(`${API_URL}/competitions`);
    return response.json();
  },
  
  create: async (data: { nom: string; championnatId: number }): Promise<Competition> => {
    const response = await fetch(`${API_URL}/competitions`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data)
    });
    return response.json();
  },
  
  delete: async (id: number): Promise<void> => {
    await fetch(`${API_URL}/competitions/${id}`, { method: 'DELETE' });
  }
};

// Epreuves API
export const epreuvesApi = {
  getAll: async (): Promise<Epreuve[]> => {
    const response = await fetch(`${API_URL}/epreuves`);
    return response.json();
  },
  
  create: async (data: { nom: string; competitionId: number }): Promise<Epreuve> => {
    const response = await fetch(`${API_URL}/epreuves`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data)
    });
    return response.json();
  },
  
  delete: async (id: number): Promise<void> => {
    await fetch(`${API_URL}/epreuves/${id}`, { method: 'DELETE' });
  }
};
