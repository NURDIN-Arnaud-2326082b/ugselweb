import { useState, useEffect } from 'react';
import { championnatsApi, competitionsApi } from '../api';
import type { Championnat } from '../api';
import './Competitions.css';

export default function Competitions() {
  const [championnats, setChampionnats] = useState<Championnat[]>([]);
  const [selectedChampionnat, setSelectedChampionnat] = useState<Championnat | null>(null);
  const [newCompetitionNom, setNewCompetitionNom] = useState('');
  const [loading, setLoading] = useState(false);

  const loadChampionnats = async () => {
    setLoading(true);
    const data = await championnatsApi.getAll();
    setChampionnats(data);
    setLoading(false);
  };

  const loadChampionnatDetails = async (id: number) => {
    const data = await championnatsApi.getOne(id);
    setSelectedChampionnat(data);
  };

  useEffect(() => {
    (async () => {
      await loadChampionnats();
    })();
  }, []);

  const handleCreateCompetition = async (e: {preventDefault: () => void}) => {
    e.preventDefault();
    if (!selectedChampionnat || !newCompetitionNom.trim()) return;
    
    await competitionsApi.create({ nom: newCompetitionNom, championnatId: selectedChampionnat.id });
    setNewCompetitionNom('');
    loadChampionnatDetails(selectedChampionnat.id);
  };

  const handleDeleteCompetition = async (id: number) => {
    if (!confirm('Supprimer cette compétition et toutes ses épreuves ?')) return;
    await competitionsApi.delete(id);
    if (selectedChampionnat) loadChampionnatDetails(selectedChampionnat.id);
  };

  return (
    <div className="competitions-container">
      <div className="championnats-panel">
        <h2>Championnats</h2>

        {loading ? <p>Chargement...</p> : (
          <div className="items-list">
            {championnats.map(championnat => (
              <button 
                key={championnat.id} 
                className={`item ${selectedChampionnat?.id === championnat.id ? 'selected' : ''}`}
                onClick={() => loadChampionnatDetails(championnat.id)}
              >
                <div className="item-info">
                  <strong>{championnat.nom}</strong>
                  <small>{championnat.sport?.nom} • {championnat.competitionsCount} compétition(s)</small>
                </div>
              </button>
            ))}
          </div>
        )}
      </div>

      {selectedChampionnat && (
        <div className="details-panel">
          <h2>Compétitions - {selectedChampionnat.nom}</h2>
          <small className="sport-name">Sport: {selectedChampionnat.sport?.nom}</small>
          
          <form onSubmit={handleCreateCompetition} className="create-form">
            <input
              type="text"
              value={newCompetitionNom}
              onChange={(e) => setNewCompetitionNom(e.target.value)}
              placeholder="Nom de la compétition"
              required
            />
            <button type="submit">+ Ajouter</button>
          </form>

          <div className="items-list">
            {selectedChampionnat.competitions?.map(competition => (
              <div key={competition.id} className="item">
                <div className="item-info">
                  <strong>{competition.nom}</strong>
                  <small>{competition.epreuvesCount} épreuve(s)</small>
                </div>
                <button 
                  onClick={() => handleDeleteCompetition(competition.id)}
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
