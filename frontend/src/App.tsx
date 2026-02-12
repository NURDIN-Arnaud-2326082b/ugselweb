import { useState } from 'react'
import Sports from './components/Sports'
import Competitions from './components/Competitions'
import Epreuves from './components/Epreuves'
import './App.css'

type Tab = 'sports' | 'competitions' | 'epreuves';

function App() {
  const [activeTab, setActiveTab] = useState<Tab>('sports');

  return (
    <div className="app">
      <header className="app-header">
        <h1>⚽ Gestion des Sports UGSEL</h1>
        <nav className="tabs">
          <button 
            className={activeTab === 'sports' ? 'active' : ''}
            onClick={() => setActiveTab('sports')}
          >
            Sports & Championnats
          </button>
          <button 
            className={activeTab === 'competitions' ? 'active' : ''}
            onClick={() => setActiveTab('competitions')}
          >
            Compétitions
          </button>
          <button 
            className={activeTab === 'epreuves' ? 'active' : ''}
            onClick={() => setActiveTab('epreuves')}
          >
            Épreuves
          </button>
        </nav>
      </header>

      <main>
        {activeTab === 'sports' && <Sports />}
        {activeTab === 'competitions' && <Competitions />}
        {activeTab === 'epreuves' && <Epreuves />}
      </main>
    </div>
  )
}

export default App
