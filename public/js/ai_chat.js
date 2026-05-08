/**
 * SANADK - AI Chat Module (Dynamic Version)
 */

class AIChat {
    constructor() {
        this.messagesContainer = document.getElementById('messagesContainer');
        this.messageInput = document.getElementById('messageInput');
        this.sendButton = document.getElementById('sendButton');
        this.isLoading = false;
        
        this.initializeEventListeners();
    }
    
    initializeEventListeners() {
        if (this.sendButton) {
            this.sendButton.addEventListener('click', () => this.sendMessage());
        }
        if (this.messageInput) {
            this.messageInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') this.sendMessage();
            });
        }
    }
    
    async sendMessage() {
        const message = this.messageInput.value.trim();
        if (!message || this.isLoading) return;
        
        this.isLoading = true;
        this.addMessageToUI(message, 'user');
        this.messageInput.value = '';
        
        try {
            const response = await fetch('/api/ai/chat', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ message: message })
            });
            
            const data = await response.json();
            if (data.success) {
                this.addMessageToUI(data.message, 'assistant');
            } else {
                this.addMessageToUI('عذراً، واجهت مشكلة في الرد.', 'assistant');
            }
        } catch (error) {
            this.addMessageToUI('خطأ في الاتصال بالخادم.', 'assistant');
        } finally {
            this.isLoading = false;
        }
    }
    
    addMessageToUI(message, role) {
        if (!this.messagesContainer) return;
        const div = document.createElement('div');
        div.className = `message ${role}-message`;
        div.innerHTML = `<div class="message-bubble">${message}</div>`;
        this.messagesContainer.appendChild(div);
        this.messagesContainer.scrollTop = this.messagesContainer.scrollHeight;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('messagesContainer')) {
        window.aiChat = new AIChat();
    }
});
