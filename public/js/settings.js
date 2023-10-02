    const addEditModal = document.querySelector('#add-edit-modal');
    const addEditTitle = document.querySelector('#add-edit-title');
    const addEditForm = document.querySelector('#add-edit-form');
    const newCategoryName = document.querySelector('#new-category-name');
    const addEditLabel = document.querySelector('#add-edit-label');
    const firstformElement = document.querySelector('#first-form-element');
    const secondformElement = document.querySelector('#second-form-element');
    const addEditMessage = document.querySelector('#add-edit-message');
    const submitFormBtn = document.querySelector('#submit-form-btn');
    
    const removeEntriesModal = new bootstrap.Modal(document.querySelector('#remove-entries-modal'));
    const removeCategoryBtn = document.querySelector('#remove-category-btn');
    
    const confirmationModal = new bootstrap.Modal(document.querySelector('#confirmation-modal'));
    const confirmationTitle = document.querySelector('#confirmation-title');
    const confirmationBody = document.querySelector('#confirmation-body');
    
    // Functions
    const prepareForm = (action, category, id) => {
        
        const capitalizedAction = action.charAt(0).toUpperCase() + action.slice(1);
    
        addEditModal.setAttribute('data-action', action);
        addEditModal.setAttribute('data-category', category);
    
        if (id !== 0) {
            addEditModal.setAttribute('data-id', id);
        }
    
        if (action == 'add') {
            addEditTitle.innerText = `Dodawanie kategorii`;
            addEditLabel.innerText = `Wpisz nazwę kategorii:`;
            } else {
                addEditTitle.innerText = `Edytowanie kategorii`;
                addEditLabel.innerText = `Edytuj nazwę kategorii:`;
            }
        submitFormBtn.innerText = `Zatwierdź`;
    
        if (category == 'expense') {
            // here I should await limit
            appendExpenseElements();
        }
    }
    
    const handleFormSubmit = async function (e) {
        const action = addEditModal.getAttribute('data-action');
        const category = addEditModal.getAttribute('data-category');
        const id = addEditModal.getAttribute('data-id');
    
        e.preventDefault();
    
        let formData = new FormData(this);
    
        try {
            const res = await fetch(`../settings/${action}-${category}-category${id ? '/' + id : ''}`, {
                method: 'POST',
                body: formData
            });
        
            if (res.ok) {
                const data = await res.json();
    
                if (data.message_type == 'error') {
                    addEditMessage.innerText = data.message;
                }
    
                if (data.message_type == 'success') {
                    sessionStorage.setItem('action', `${action}`);
                    location.reload();
                }
            } else {
                throw new Error('Request failed. Status: ' + res.status);
            }
        } catch (error) {
            console.error(error);
        }
    }
    
    const checkRemoveCategory = async (category, categoryId) => {
        try {
            const res = await fetch(`../settings/check-remove-${category}-category/${categoryId}`);
            const data = await res.json();
    
            if (data.message_type == 'warning') {
                removeEntriesModal.show();
                removeCategoryBtn.addEventListener('click', () => removeCategory(category, categoryId));
            }
    
            if (data.message_type == 'success') {
                sessionStorage.setItem('action', 'remove');
                location.reload();
            }
        } catch (data) {
            console.error(data.message);
        }
    }
    
    const removeCategory = async (category, categoryId) => {
        try {
            const res = await fetch(`../settings/remove-${category}-category/${categoryId}`);
            const data = await res.json();
    
            if (data.message_type == 'success') {
                sessionStorage.setItem('action', 'remove');
                location.reload();
            }
        } catch (data) {
            console.error(data.message);
        }
    }
    
    const clearInput = () => {
        addEditForm.reset();
        addEditMessage.innerText = '';
    
        const elements = document.querySelectorAll('.additional');
    
        elements.forEach(element => {
            element.remove();
        });
    
        newCategoryName.required = true;
    }
    
    const showConfirmation = () => {
        const action = sessionStorage.getItem('action');
    
        if (action) {
            prepareConfirmation(action);
            confirmationModal.show();
            sessionStorage.removeItem('action');
        }
    }
    
    const prepareConfirmation = action => {
    
        if (action == 'add') {
            confirmationTitle.innerText = `Kategoria dodana`;
            confirmationBody.innerText = `Kategoria odpowiednio  dodana!`;
        } else if (action == 'edit'){
            confirmationTitle.innerText = `Kategoria edytowana`;
            confirmationBody.innerText = `Kategoria odpowiednio  edytowana!`;
        }
        else  {
            confirmationTitle.innerText = `Kategoria usunięta`;
            confirmationBody.innerText = `Kategoria odpowiednio  usunięta!`;
        }
    }
    
    addEditForm.addEventListener('submit', handleFormSubmit);
    addEditModal.addEventListener('hidden.bs.modal', clearInput);
    document.addEventListener('DOMContentLoaded', showConfirmation);
    