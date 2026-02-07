class CategoryService {
    constructor() {
        this.baseUrl = 'http://127.0.0.1:8000/api/v1/owner/restaurant';
        this.token = localStorage.getItem('auth_token');
        this.cache = null;
    }

    async fetchCategories(forceRefresh = false) {
        if (this.cache && !forceRefresh) return this.cache;

        try {
            const res = await fetch(`${this.baseUrl}/categories`, {
                headers: { 'Authorization': `Bearer ${this.token}` }
            });
            const data = await res.json();
            if (!res.ok) throw data;

            this.cache = data.data;
            return this.cache;
        } catch (err) {
            console.error('Failed to fetch categories:', err);
            return [];
        }
    }

    async populateSelect(selectElement, selectedId = null) {
        const categories = await this.fetchCategories();

        selectElement.innerHTML = '<option value="">Select category</option>';

        if (categories.length === 0) {
            selectElement.innerHTML = '<option value="">No categories found</option>';
            return;
        }

        categories.forEach(cat => {
            const option = document.createElement('option');
            option.value = cat.id;
            option.textContent = cat.name;
            if (selectedId && cat.id === selectedId) option.selected = true;
            selectElement.appendChild(option);
        });
    }

    clearCache() {
        this.cache = null;
    }
}

// Expose globally
window.CategoryService = CategoryService;
