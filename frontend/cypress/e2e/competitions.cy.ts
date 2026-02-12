describe('Competitions Management', () => {
  beforeEach(() => {
    cy.visit('/');
    // Navigate to competitions tab
    cy.contains('Compétitions').click();
  });

  it('should display the competitions page', () => {
    cy.contains('h2', 'Championnats').should('be.visible');
  });

  it('should select a championship and display its competitions', () => {
    // Wait for championships to load
    cy.get('.items-list .item').first().click();
    
    // Should display competitions panel
    cy.contains('h2', /Compétitions -/).should('be.visible');
  });

  it('should create a new competition', () => {
    const competitionName = `Compétition Test ${Date.now()}`;
    
    // Select first championship
    cy.get('.items-list .item').first().click();
    
    // Wait for details panel
    cy.contains('h2', /Compétitions -/).should('be.visible');
    
    // Create competition
    cy.get('.details-panel input[placeholder="Nom de la compétition"]').type(competitionName);
    cy.get('.details-panel button[type="submit"]').contains('Ajouter').click();
    
    // Verify competition was created
    cy.get('.details-panel').contains('.item', competitionName).should('be.visible');
  });

  it('should delete a competition', () => {
    // Select first championship
    cy.get('.items-list .item').first().click();
    cy.contains('h2', /Compétitions -/).should('be.visible');
    
    // Create a competition to delete
    const competitionName = `Compétition à supprimer ${Date.now()}`;
    cy.get('.details-panel input[placeholder="Nom de la compétition"]').type(competitionName);
    cy.get('.details-panel button[type="submit"]').contains('Ajouter').click();
    cy.get('.details-panel').contains('.item', competitionName).should('be.visible');
    
    // Stub confirm dialog
    cy.on('window:confirm', () => true);
    
    // Delete the competition
    cy.get('.details-panel').contains('.item', competitionName).find('.delete-btn').click();
    
    // Verify competition was deleted
    cy.get('.details-panel').contains('.item', competitionName).should('not.exist');
  });

  it('should display championship information', () => {
    // Select first championship
    cy.get('.items-list .item').first().click();
    
    // Should show sport name
    cy.get('.sport-name').should('contain', 'Sport:');
  });
});
