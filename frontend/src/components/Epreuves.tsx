import { useState, useEffect } from 'react';
import { competitionsApi, epreuvesApi } from '../api';
import type { Competition, Epreuve } from '../api';
import './Epreuves.css';

export default function Epreuves() {
  const [competitions, setCompetitions] = useState<Competition[]>([]);
  const [selectedCompetition, setSelectedCompetition] = useState<Competition | null>(null);
  const [epreuves, setEpreuves] = useState<Epreuve[]>([]);
  const [newEpreuveNom, setNewEpreuveNom] = useState('');
  const [loading, setLoading] = useState(false);

  const loadEpreuves = async () => {
    const data = await epreuvesApi.getAll();
    setEpreuves(data);
  };

  const getEpreuvesForCompetition = (competitionId: number) => {
    return epreuves.filter(e => e.competition?.id === competitionId);
  };

  useEffect(() => {
    const fetchData = async () => {
      setLoading(true);
      const [compsData, epreuvesData] = await Promise.all([
        competitionsApi.getAll(),
        epreuvesApi.getAll()
      ]);
      setCompetitions(compsData);
      setEpreuves(epreuvesData);
      setLoading(false);
    };
    void fetchData();
  }, []);

  const handleCreateEpreuve = async (e: {preventDefault: () => void}) => {
    e.preventDefault();
    if (!selectedCompetition || !newEpreuveNom.trim()) return;
    
    await epreuvesApi.create({ nom: newEpreuveNom, competitionId: selectedCompetition.id });
    setNewEpreuveNom('');
    loadEpreuves();
  };

  const handleDeleteEpreuve = async (id: number) => {
    if (!confirm('Supprimer cette épreuve ?')) return;
    await epreuvesApi.delete(id);
    loadEpreuves();
  };

  return (
    <div className="epreuves-container">
      <div className="competitions-panel">
        <h2>Compétitions</h2>

        {loading ? <p>Chargement...</p> : (
          <div className="items-list">
            {competitions.map(competition => (
              <button 
                key={competition.id} 
                className={`item ${selectedCompetition?.id === competition.id ? 'selected' : ''}`}
                onClick={() => setSelectedCompetition(competition)}
              >
                <div className="item-info">
                  <strong>{competition.nom}</strong>
                  <small>
                    {competition.championnat?.nom}
                    {competition.championnat?.sport && ` • ${competition.championnat.sport.nom}`}
                    <br />
                    {competition.epreuvesCount} épreuve(s)
                  </small>
                </div>
              </button>
            ))}
          </div>
        )}
      </div>

      {selectedCompetition && (
        <div className="details-panel">
          <h2>Épreuves - {selectedCompetition.nom}</h2>
          <small className="competition-info">
            {selectedCompetition.championnat?.nom}
            {selectedCompetition.championnat?.sport && ` • ${selectedCompetition.championnat.sport.nom}`}
          </small>
          
          <form onSubmit={handleCreateEpreuve} className="create-form">
            <input
              type="text"
              value={newEpreuveNom}
              onChange={(e) => setNewEpreuveNom(e.target.value)}
              placeholder="Nom de l'épreuve"
              required
            />
            <button type="submit">+ Ajouter</button>
          </form>

          <div className="items-list">
            {getEpreuvesForCompetition(selectedCompetition.id).map(epreuve => (
              <div key={epreuve.id} className="item">
                <div className="item-info">
                  <strong>{epreuve.nom}</strong>
                </div>
                <button 
                  onClick={() => handleDeleteEpreuve(epreuve.id)}
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
