describe('Sports Management', () => {
  beforeEach(() => {
    cy.visit('/');
  });

  it('should display the sports page', () => {
    cy.contains('h2', 'Sports').should('be.visible');
  });

  it('should create a new sport', () => {
    const sportName = `Sport Test ${Date.now()}`;
    
    // Fill form
    cy.get('input[placeholder="Nom du sport"]').type(sportName);
    cy.get('select').select('collectif');
    cy.get('button[type="submit"]').contains('Ajouter').click();
    
    // Verify sport was created
    cy.contains('.item', sportName).should('be.visible');
  });

  it('should select a sport and display its championships', () => {
    // Wait for sports to load
    cy.get('.items-list .item').first().click();
    
    // Should display championships panel
    cy.contains('h2', /Championnats de/).should('be.visible');
  });

  it('should create a championship for a sport', () => {
    const championnatName = `Championnat Test ${Date.now()}`;
    
    // Select a sport
    cy.get('.items-list .item').first().click();
    
    // Wait for details panel
    cy.contains('h2', /Championnats de/).should('be.visible');
    
    // Create championship
    cy.get('.details-panel input[placeholder="Nom du championnat"]').type(championnatName);
    cy.get('.details-panel button[type="submit"]').contains('Ajouter').click();
    
    // Verify championship was created
    cy.get('.details-panel').contains('.item', championnatName).should('be.visible');
  });

  it('should delete a sport', () => {
    // Create a sport to delete
    const sportName = `Sport à supprimer ${Date.now()}`;
    cy.get('input[placeholder="Nom du sport"]').type(sportName);
    cy.get('button[type="submit"]').contains('Ajouter').click();
    
    // Wait for sport to appear
    cy.contains('.item', sportName).should('be.visible');
    
    // Stub the confirm dialog
    cy.on('window:confirm', () => true);
    
    // Delete the sport
    cy.contains('.item', sportName).find('.delete-btn').click();
    
    // Verify sport was deleted
    cy.contains('.item', sportName).should('not.exist');
  });

  it('should delete a championship', () => {
    // Select first sport
    cy.get('.items-list .item').first().click();
    cy.contains('h2', /Championnats de/).should('be.visible');
    
    // Create a championship to delete
    const championnatName = `Championnat à supprimer ${Date.now()}`;
    cy.get('.details-panel input[placeholder="Nom du championnat"]').type(championnatName);
    cy.get('.details-panel button[type="submit"]').contains('Ajouter').click();
    cy.get('.details-panel').contains('.item', championnatName).should('be.visible');
    
    // Stub confirm dialog
    cy.on('window:confirm', () => true);
    
    // Delete the championship
    cy.get('.details-panel').contains('.item', championnatName).find('.delete-btn').click();
    
    // Verify championship was deleted
    cy.get('.details-panel').contains('.item', championnatName).should('not.exist');
  });
});
