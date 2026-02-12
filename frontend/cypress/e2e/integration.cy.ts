describe('Full Integration Test', () => {
  const timestamp = Date.now();
  const sportName = `Sport Integration ${timestamp}`;
  const championnatName = `Championnat Integration ${timestamp}`;
  const competitionName = `Compétition Integration ${timestamp}`;
  const epreuveName = `Épreuve Integration ${timestamp}`;

  it('should create a complete hierarchy: Sport > Championnat > Compétition > Épreuve', () => {
    cy.visit('/');
    
    // Step 1: Create a sport
    cy.contains('h2', 'Sports').should('be.visible');
    cy.get('input[placeholder="Nom du sport"]').type(sportName);
    cy.get('select').select('collectif');
    cy.get('button[type="submit"]').contains('Ajouter').click();
    cy.contains('.item', sportName).should('be.visible');
    
    // Step 2: Create a championship for this sport
    cy.contains('.item', sportName).click();
    cy.contains('h2', `Championnats de ${sportName}`).should('be.visible');
    cy.get('.details-panel input[placeholder="Nom du championnat"]').type(championnatName);
    cy.get('.details-panel button[type="submit"]').contains('Ajouter').click();
    cy.get('.details-panel').contains('.item', championnatName).should('be.visible');
    
    // Step 3: Create a competition for this championship
    cy.contains('Compétitions').click();
    cy.contains('h2', 'Championnats').should('be.visible');
    cy.contains('.item', championnatName).click();
    cy.contains('h2', `Compétitions - ${championnatName}`).should('be.visible');
    cy.get('.details-panel input[placeholder="Nom de la compétition"]').type(competitionName);
    cy.get('.details-panel button[type="submit"]').contains('Ajouter').click();
    cy.get('.details-panel').contains('.item', competitionName).should('be.visible');
    
    // Step 4: Create an épreuve for this competition
    cy.contains('Épreuves').click();
    cy.contains('h2', 'Compétitions').should('be.visible');
    cy.contains('button.item', competitionName).click();
    cy.contains('h2', `Épreuves - ${competitionName}`).should('be.visible');
    cy.get('.details-panel input[placeholder="Nom de l\'épreuve"]').type(epreuveName);
    cy.get('.details-panel button[type="submit"]').contains('Ajouter').click();
    cy.get('.details-panel').contains('.item', epreuveName).should('be.visible');
    
    // Step 5: Verify the complete hierarchy is visible
    cy.get('.competition-info').should('contain', championnatName);
    cy.get('.competition-info').should('contain', sportName);
  });

  it('should display correct counts in hierarchy', () => {
    cy.visit('/');
    
    // Check sport shows championship count
    cy.contains('.item', sportName).within(() => {
      cy.get('small').should('contain', '1 championnat');
    });
    
    // Navigate to competitions and check
    cy.contains('Compétitions').click();
    cy.contains('.item', championnatName).within(() => {
      cy.get('small').should('contain', '1 compétition');
    });
    
    // Navigate to épreuves and check
    cy.contains('Épreuves').click();
    cy.contains('button.item', competitionName).within(() => {
      cy.get('small').should('contain', '1 épreuve');
    });
  });
});
