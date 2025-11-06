// Gestionnaire de contacts asynchrones avec fetch
class ContactManager {
    constructor() {
        this.apiUrls = {
            contacts: 'api_contacts.php',
            add: 'api_add.php',
            delete: 'api_delete.php'
        };
        
        this.elements = {
            toggleFormBtn: document.getElementById('toggleFormBtn'),
            addContactForm: document.getElementById('addContactForm'),
            submitBtn: document.getElementById('submitBtn'),
            cancelBtn: document.getElementById('cancelBtn'),
            loadingIndicator: document.getElementById('loadingIndicator'),
            contactsTableBody: document.getElementById('contactsTableBody'),
            messageContainer: document.getElementById('messageContainer')
        };
        
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.loadContacts();
    }
    
    bindEvents() {
        // Toggle du formulaire d'ajout
        this.elements.toggleFormBtn.addEventListener('click', () => {
            this.toggleForm();
        });
        
        // Annulation du formulaire
        this.elements.cancelBtn.addEventListener('click', () => {
            this.hideForm();
        });
        
        // Soumission du formulaire
        this.elements.addContactForm.addEventListener('submit', (e) => {
            e.preventDefault();
            this.addContact();
        });
    }
    
    toggleForm() {
        const form = this.elements.addContactForm;
        const isVisible = form.style.display !== 'none';
        
        if (isVisible) {
            this.hideForm();
        } else {
            this.showForm();
        }
    }
    
    showForm() {
        this.elements.addContactForm.style.display = 'block';
        this.elements.toggleFormBtn.textContent = '✕ Fermer le formulaire';
        this.clearForm();
    }
    
    hideForm() {
        this.elements.addContactForm.style.display = 'none';
        this.elements.toggleFormBtn.textContent = '➕ Ajouter un contact';
        this.clearForm();
    }
    
    clearForm() {
        this.elements.addContactForm.reset();
        this.hideMessage();
    }
    
    // Chargement des contacts via fetch
    async loadContacts() {
        try {
            this.showLoading();
            
            const response = await fetch(this.apiUrls.contacts, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                }
            });
            
            if (!response.ok) {
                throw new Error(`Erreur HTTP: ${response.status}`);
            }
            
            const data = await response.json();
            
            if (data.success) {
                this.renderContacts(data.data);
            } else {
                this.showMessage('Erreur lors du chargement des contacts: ' + data.message, 'error');
            }
            
        } catch (error) {
            console.error('Erreur lors du chargement des contacts:', error);
            this.showMessage('Erreur de connexion lors du chargement des contacts', 'error');
            this.renderContacts([]);
        } finally {
            this.hideLoading();
        }
    }
    
    // Ajout d'un contact via fetch
    async addContact() {
        try {
            const formData = new FormData(this.elements.addContactForm);
            const contactData = {
                nom: formData.get('nom'),
                email: formData.get('email'),
                telephone: formData.get('telephone')
            };
            
            // Validation côté client
            if (!contactData.nom || !contactData.email) {
                this.showMessage('Le nom et l\'email sont requis', 'error');
                return;
            }
            
            this.setFormLoading(true);
            
            const response = await fetch(this.apiUrls.add, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(contactData)
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showMessage('Contact ajouté avec succès!', 'success');
                this.hideForm();
                await this.loadContacts(); // Recharger la liste
            } else {
                this.showMessage('Erreur: ' + data.message, 'error');
            }
            
        } catch (error) {
            console.error('Erreur lors de l\'ajout du contact:', error);
            this.showMessage('Erreur de connexion lors de l\'ajout du contact', 'error');
        } finally {
            this.setFormLoading(false);
        }
    }
    
    // Suppression d'un contact via fetch
    async deleteContact(id, contactName) {
        if (!confirm(`Voulez-vous vraiment supprimer le contact "${contactName}" ?`)) {
            return;
        }
        
        try {
            const response = await fetch(this.apiUrls.delete, {
                method: 'POST', // Utiliser POST pour une meilleure compatibilité
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id: id })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showMessage('Contact supprimé avec succès!', 'success');
                await this.loadContacts(); // Recharger la liste
            } else {
                this.showMessage('Erreur lors de la suppression: ' + data.message, 'error');
            }
            
        } catch (error) {
            console.error('Erreur lors de la suppression du contact:', error);
            this.showMessage('Erreur de connexion lors de la suppression du contact', 'error');
        }
    }
    
    // Rendu des contacts dans le tableau
    renderContacts(contacts) {
        const tbody = this.elements.contactsTableBody;
        
        if (contacts.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" class="no-contacts">Aucun contact trouvé</td></tr>';
            return;
        }
        
        tbody.innerHTML = contacts.map(contact => `
            <tr data-contact-id="${contact.id}">
                <td>${this.escapeHtml(contact.nom)}</td>
                <td>${this.escapeHtml(contact.email)}</td>
                <td>${this.escapeHtml(contact.telephone || '')}</td>
                <td>
                    <button class="delete-btn" onclick="contactManager.deleteContact('${contact.id}', '${this.escapeHtml(contact.nom)}')">
                        🗑️ Supprimer
                    </button>
                </td>
            </tr>
        `).join('');
    }
    
    // Utilitaires pour l'interface
    showLoading() {
        this.elements.loadingIndicator.style.display = 'block';
    }
    
    hideLoading() {
        this.elements.loadingIndicator.style.display = 'none';
    }
    
    setFormLoading(loading) {
        const submitBtn = this.elements.submitBtn;
        const btnText = submitBtn.querySelector('.btn-text');
        const loadingText = submitBtn.querySelector('.loading');
        
        if (loading) {
            btnText.style.display = 'none';
            loadingText.style.display = 'inline';
            submitBtn.disabled = true;
        } else {
            btnText.style.display = 'inline';
            loadingText.style.display = 'none';
            submitBtn.disabled = false;
        }
    }
    
    showMessage(message, type = 'info') {
        const container = this.elements.messageContainer;
        container.innerHTML = `
            <div class="message message-${type}">
                ${message}
                <button class="message-close" onclick="contactManager.hideMessage()">✕</button>
            </div>
        `;
        container.style.display = 'block';
        
        // Auto-hide après 5 secondes pour les messages de succès
        if (type === 'success') {
            setTimeout(() => {
                this.hideMessage();
            }, 5000);
        }
    }
    
    hideMessage() {
        this.elements.messageContainer.style.display = 'none';
        this.elements.messageContainer.innerHTML = '';
    }
    
    // Échapper les caractères HTML pour éviter les injections XSS
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Initialiser le gestionnaire de contacts quand le DOM est chargé
document.addEventListener('DOMContentLoaded', () => {
    window.contactManager = new ContactManager();
});

// Fonction globale pour la compatibilité (appelée depuis les boutons générés dynamiquement)
window.deleteContact = (id, name) => {
    if (window.contactManager) {
        window.contactManager.deleteContact(id, name);
    }
};