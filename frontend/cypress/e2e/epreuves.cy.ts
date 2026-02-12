describe('Épreuves Management', () => {
  beforeEach(() => {
    cy.visit('/');
    // Navigate to épreuves tab
    cy.contains('Épreuves').click();
  });

  it('should display the épreuves page', () => {
    cy.contains('h2', 'Compétitions').should('be.visible');
  });

  it('should select a competition and display its épreuves', () => {
    // Wait for competitions to load
    cy.get('.items-list button.item').first().click();
    
    // Should display épreuves panel
    cy.contains('h2', /Épreuves -/).should('be.visible');
  });

  it('should create a new épreuve', () => {
    const epreuveName = `Épreuve Test ${Date.now()}`;
    
    // Select first competition
    cy.get('.items-list button.item').first().click();
    
    // Wait for details panel
    cy.contains('h2', /Épreuves -/).should('be.visible');
    
    // Create épreuve
    cy.get('.details-panel input[placeholder="Nom de l\'épreuve"]').type(epreuveName);
    cy.get('.details-panel button[type="submit"]').contains('Ajouter').click();
    
    // Verify épreuve was created
    cy.get('.details-panel').contains('.item', epreuveName).should('be.visible');
  });

  it('should delete an épreuve', () => {
    // Select first competition
    cy.get('.items-list button.item').first().click();
    cy.contains('h2', /Épreuves -/).should('be.visible');
    
    // Create an épreuve to delete
    const epreuveName = `Épreuve à supprimer ${Date.now()}`;
    cy.get('.details-panel input[placeholder="Nom de l\'épreuve"]').type(epreuveName);
    cy.get('.details-panel button[type="submit"]').contains('Ajouter').click();
    cy.get('.details-panel').contains('.item', epreuveName).should('be.visible');
    
    // Stub confirm dialog
    cy.on('window:confirm', () => true);
    
    // Delete the épreuve
    cy.get('.details-panel').contains('.item', epreuveName).find('.delete-btn').click();
    
    // Verify épreuve was deleted
    cy.get('.details-panel').contains('.item', epreuveName).should('not.exist');
  });

  it('should display competition information', () => {
    // Select first competition
    cy.get('.items-list button.item').first().click();
    
    // Should show competition info (championnat and sport)
    cy.get('.competition-info').should('exist');
  });

  it('should list all competitions with their details', () => {
    // Should show competitions with championship and sport info
    cy.get('.items-list button.item').first().within(() => {
      cy.get('.item-info').should('exist');
      cy.get('strong').should('exist'); // Competition name
      cy.get('small').should('exist'); // Championship and sport info
    });
  });
});
