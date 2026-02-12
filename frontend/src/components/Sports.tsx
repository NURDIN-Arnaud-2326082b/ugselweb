import { useState, useEffect } from 'react';
import { sportsApi, championnatsApi } from '../api';
import type { Sport } from '../api';
import './Sports.css';

export default function Sports() {
  const [sports, setSports] = useState<Sport[]>([]);
  const [selectedSport, setSelectedSport] = useState<Sport | null>(null);
  const [newSportNom, setNewSportNom] = useState('');
  const [newSportType, setNewSportType] = useState<'individuel' | 'collectif'>('individuel');
  const [newChampionnatNom, setNewChampionnatNom] = useState('');
  const [loading, setLoading] = useState(false);

  const loadSports = async () => {
    setLoading(true);
    const data = await sportsApi.getAll();
    setSports(data);
    setLoading(false);
  };

  const loadSportDetails = async (id: number) => {
    const data = await sportsApi.getOne(id);
    setSelectedSport(data);
  };

  useEffect(() => {
    (async () => {
      await loadSports();
    })();
  }, []);

  const handleCreateSport = async (e: {preventDefault: () => void}) => {
    e.preventDefault();
    if (!newSportNom.trim()) return;
    
    await sportsApi.create({ nom: newSportNom, type: newSportType });
    setNewSportNom('');
    loadSports();
  };

  const handleDeleteSport = async (id: number) => {
    if (!confirm('Supprimer ce sport et tous ses championnats ?')) return;
    await sportsApi.delete(id);
    if (selectedSport?.id === id) setSelectedSport(null);
    loadSports();
  };

  const handleCreateChampionnat = async (e: {preventDefault: () => void}) => {
    e.preventDefault();
    if (!selectedSport || !newChampionnatNom.trim()) return;
    
    await championnatsApi.create({ nom: newChampionnatNom, sportId: selectedSport.id });
    setNewChampionnatNom('');
    loadSportDetails(selectedSport.id);
  };

  const handleDeleteChampionnat = async (id: number) => {
    if (!confirm('Supprimer ce championnat et toutes ses compétitions ?')) return;
    await championnatsApi.delete(id);
    if (selectedSport) loadSportDetails(selectedSport.id);
  };

  return (
    <div className="sports-container">
      <div className="sports-panel">
        <h2>Sports</h2>
        
        <form onSubmit={handleCreateSport} className="create-form">
          <input
            type="text"
            value={newSportNom}
            onChange={(e) => setNewSportNom(e.target.value)}
            placeholder="Nom du sport"
            required
          />
          <select value={newSportType} onChange={(e) => setNewSportType(e.target.value as 'individuel' | 'collectif')}>
            <option value="individuel">Individuel</option>
            <option value="collectif">Collectif</option>
          </select>
          <button type="submit">+ Ajouter</button>
        </form>

        {loading ? <p>Chargement...</p> : (
          <div className="items-list">
            {sports.map(sport => (
              <button 
                key={sport.id} 
                className={`item ${selectedSport?.id === sport.id ? 'selected' : ''}`}
                onClick={() => loadSportDetails(sport.id)}
              >
                <div className="item-info">
                  <strong>{sport.nom}</strong>
                  <small>{sport.type} • {sport.championnatsCount} championnat(s)</small>
                </div>
                <button 
                  onClick={(e) => { e.stopPropagation(); handleDeleteSport(sport.id); }}
                  className="delete-btn"
                >
                  🗑️
                </button>
              </button>
            ))}
          </div>
        )}
      </div>

      {selectedSport && (
        <div className="details-panel">
          <h2>Championnats de {selectedSport.nom}</h2>
          
          <form onSubmit={handleCreateChampionnat} className="create-form">
            <input
              type="text"
              value={newChampionnatNom}
              onChange={(e) => setNewChampionnatNom(e.target.value)}
              placeholder="Nom du championnat"
              required
            />
            <button type="submit">+ Ajouter</button>
          </form>

          <div className="items-list">
            {selectedSport.championnats?.map(championnat => (
              <div key={championnat.id} className="item">
                <div className="item-info">
                  <strong>{championnat.nom}</strong>
                  <small>{championnat.competitionsCount} compétition(s)</small>
                </div>
                <button 
                  onClick={() => handleDeleteChampionnat(championnat.id)}
                  className="delete-btn"
                >
                  🗑️
                </button>
              </div>
            ))}
          </div>
        </div>
      )}
    </div>
  );
}
