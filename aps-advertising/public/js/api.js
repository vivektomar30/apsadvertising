// APS Advertising - API Configuration and Helper Functions
// This file handles all backend communication

const API_CONFIG = {
    // Change this to your production URL when deploying
    BASE_URL: window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1'
        ? 'http://localhost:5000/backend/api/index.php'
        : '/api',

    // Request timeout in milliseconds
    TIMEOUT: 30000
};

// API Helper Class
class APSApi {
    constructor() {
        this.baseUrl = API_CONFIG.BASE_URL;
    }

    // Get auth token from localStorage
    getToken() {
        return localStorage.getItem('aps_token');
    }

    // Set auth token
    setToken(token) {
        localStorage.setItem('aps_token', token);
    }

    // Remove auth token
    removeToken() {
        localStorage.removeItem('aps_token');
    }

    // Generic fetch with timeout and error handling
    async request(endpoint, options = {}) {
        const url = `${this.baseUrl}${endpoint}`;

        const defaultHeaders = {
            'Content-Type': 'application/json'
        };

        // Add auth header if token exists
        const token = this.getToken();
        if (token) {
            defaultHeaders['Authorization'] = `Bearer ${token}`;
        }

        const config = {
            ...options,
            headers: {
                ...defaultHeaders,
                ...options.headers
            }
        };

        try {
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), API_CONFIG.TIMEOUT);

            const response = await fetch(url, {
                ...config,
                signal: controller.signal
            });

            clearTimeout(timeoutId);

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Something went wrong');
            }

            return data;
        } catch (error) {
            if (error.name === 'AbortError') {
                throw new Error('Request timeout. Please try again.');
            }
            throw error;
        }
    }

    // GET request
    async get(endpoint, params = {}) {
        const queryString = new URLSearchParams(params).toString();
        const url = queryString ? `${endpoint}?${queryString}` : endpoint;
        return this.request(url, { method: 'GET' });
    }

    // POST request
    async post(endpoint, data = {}) {
        return this.request(endpoint, {
            method: 'POST',
            body: JSON.stringify(data)
        });
    }

    // PUT request
    async put(endpoint, data = {}) {
        return this.request(endpoint, {
            method: 'PUT',
            body: JSON.stringify(data)
        });
    }

    // DELETE request
    async delete(endpoint) {
        return this.request(endpoint, { method: 'DELETE' });
    }

    // ==================== AUTH API ====================
    async login(email, password) {
        const response = await this.post('/auth/login', { email, password });
        if (response.token) {
            this.setToken(response.token);
        }
        return response;
    }

    async register(name, email, password) {
        return this.post('/auth/register', { name, email, password });
    }

    async getMe() {
        return this.get('/auth/me');
    }

    async logout() {
        this.removeToken();
        return { success: true, message: 'Logged out successfully' };
    }

    // ==================== CONTACT API ====================
    async submitContact(contactData) {
        return this.post('/contact', contactData);
    }

    async getContacts(params = {}) {
        return this.get('/contact', params);
    }

    async getContact(id) {
        return this.get(`/contact/${id}`);
    }

    async updateContactStatus(id, status, assignedTo = null) {
        return this.put(`/contact/${id}/status`, { status, assignedTo });
    }

    async addContactNote(id, note) {
        return this.post(`/contact/${id}/notes`, { note });
    }

    async deleteContact(id) {
        return this.delete(`/contact/${id}`);
    }

    // ==================== FEEDBACK API ====================
    async submitFeedback(feedbackData) {
        return this.post('/feedback', feedbackData);
    }

    async getFeedback(params = {}) {
        return this.get('/feedback', params);
    }

    async toggleFeedbackLike(id) {
        return this.put(`/feedback/${id}/like`);
    }

    async approveFeedback(id) {
        return this.put(`/feedback/${id}/approve`);
    }

    async deleteFeedback(id) {
        return this.delete(`/feedback/${id}`);
    }

    // ==================== CONTENT API ====================
    async getContent(params = {}) {
        return this.get('/content', params);
    }

    async getPageContent(page, language = 'en') {
        return this.get(`/content/page/${page}`, { language });
    }

    async createContent(contentData) {
        return this.post('/content', contentData);
    }

    async updateContent(id, contentData) {
        return this.put(`/content/${id}`, contentData);
    }

    async deleteContent(id) {
        return this.delete(`/content/${id}`);
    }

    // ==================== HEALTH CHECK ====================
    async checkHealth() {
        return this.get('/health');
    }
}

// Create global API instance
const apsApi = new APSApi();

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { apsApi, API_CONFIG };
}

// Utility Functions
const APIUtils = {
    // Show notification toast
    showNotification(message, type = 'success', duration = 5000) {
        const existingNotification = document.querySelector('.api-notification');
        if (existingNotification) {
            existingNotification.remove();
        }

        const notification = document.createElement('div');
        notification.className = `api-notification api-notification-${type}`;
        notification.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
            <span>${message}</span>
            <button onclick="this.parentElement.remove()" class="notification-close">&times;</button>
        `;

        // Add styles if not already added
        if (!document.getElementById('api-notification-styles')) {
            const style = document.createElement('style');
            style.id = 'api-notification-styles';
            style.textContent = `
                .api-notification {
                    position: fixed;
                    top: 100px;
                    right: 20px;
                    padding: 15px 20px;
                    border-radius: 10px;
                    display: flex;
                    align-items: center;
                    gap: 12px;
                    z-index: 10001;
                    animation: slideInRight 0.3s ease;
                    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
                    max-width: 400px;
                    font-family: 'Poppins', sans-serif;
                }
                .api-notification-success {
                    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
                    color: white;
                }
                .api-notification-error {
                    background: linear-gradient(135deg, #FF003C 0%, #FF3366 100%);
                    color: white;
                }
                .api-notification-info {
                    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
                    color: white;
                }
                .notification-close {
                    background: transparent;
                    border: none;
                    color: white;
                    font-size: 1.5rem;
                    cursor: pointer;
                    padding: 0;
                    margin-left: auto;
                    opacity: 0.8;
                    transition: opacity 0.3s;
                }
                .notification-close:hover {
                    opacity: 1;
                }
                @keyframes slideInRight {
                    from {
                        transform: translateX(100%);
                        opacity: 0;
                    }
                    to {
                        transform: translateX(0);
                        opacity: 1;
                    }
                }
            `;
            document.head.appendChild(style);
        }

        document.body.appendChild(notification);

        setTimeout(() => {
            if (notification.parentElement) {
                notification.style.animation = 'slideInRight 0.3s ease reverse';
                setTimeout(() => notification.remove(), 300);
            }
        }, duration);
    },

    // Format date
    formatDate(dateString) {
        const options = { year: 'numeric', month: 'short', day: 'numeric' };
        return new Date(dateString).toLocaleDateString('en-US', options);
    },

    // Validate email
    isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    },

    // Validate phone (Indian format)
    isValidPhone(phone) {
        const phoneRegex = /^[6-9]\d{9}$/;
        return phoneRegex.test(phone.replace(/\D/g, ''));
    },

    // Debounce function
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    },

    // Loading state for buttons
    setButtonLoading(button, isLoading, originalText = null) {
        if (isLoading) {
            button.dataset.originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            button.disabled = true;
        } else {
            button.innerHTML = originalText || button.dataset.originalText;
            button.disabled = false;
        }
    }
};

// Initialize on page load
document.addEventListener('DOMContentLoaded', function () {
    // Check if backend is available
    apsApi.checkHealth()
        .then(response => {
            console.log('✅ Backend connected:', response.message);
        })
        .catch(error => {
            console.warn('⚠️ Backend not available. Using local storage fallback.');
        });
});

