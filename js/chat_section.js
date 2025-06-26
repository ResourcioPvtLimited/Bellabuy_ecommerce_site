function toggleChat() {
    const box = document.getElementById('chatBox');
    box.style.display = box.style.display === 'flex' ? 'none' : 'flex';
  }
  
  document.addEventListener('DOMContentLoaded', function () {
    const input = document.querySelector('.chat-footer input');
    const chatBody = document.querySelector('.chat-body');
    const typingStatus = document.getElementById('typingStatus');
  
    input.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' && input.value.trim() !== '') {
        const message = input.value.trim();
  
        // User's message
        const msgDiv = document.createElement('div');
        msgDiv.className = 'user-msg';
        msgDiv.textContent = message;
        chatBody.appendChild(msgDiv);
        chatBody.scrollTop = chatBody.scrollHeight;
        input.value = '';
  
        // Simulate Customer Care Typing
        typingStatus.textContent = 'Typing...';
  
        setTimeout(() => {
          typingStatus.textContent = '';
  
          // Customer Care reply
          const reply = document.createElement('div');
          reply.className = 'agent-msg';
          reply.textContent = 'Thanks for reaching out! We will assist you shortly.';
          chatBody.appendChild(reply);
          chatBody.scrollTop = chatBody.scrollHeight;
        }, 2000); // Simulate typing delay (2 seconds)
      }
    });
  });
  function toggleChat() {
    const chatBox = document.getElementById('chatBox');
    chatBox.style.display = chatBox.style.display === 'flex' ? 'none' : 'flex';
  }
  
  
  document.addEventListener('DOMContentLoaded', function () {
    const input = document.querySelector('.chat-footer input');
    const chatBody = document.querySelector('.chat-body');
    const typingStatus = document.getElementById('typingStatus');
  
    // Submit message on Enter
    input.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' && input.value.trim() !== '') {
        sendUserMessage(input.value.trim());
        input.value = '';
      }
    });
  
    // Handle quick reply buttons
    const quickButtons = document.querySelectorAll('.chat-options button');
    quickButtons.forEach(button => {
      button.addEventListener('click', () => {
        const text = button.textContent.trim();
        sendUserMessage(text);
      });
    });
  
    // Function to send user message and simulate reply
    function sendUserMessage(text) {
      // Add user message
      const userMsg = document.createElement('div');
      userMsg.className = 'user-msg';
      userMsg.textContent = text;
      chatBody.appendChild(userMsg);
      chatBody.scrollTop = chatBody.scrollHeight;
  
      // Show typing status
      typingStatus.textContent = 'TYPING...';
  
      // Simulate agent reply
      setTimeout(() => {
        typingStatus.textContent = '';
  
        const agentMsg = document.createElement('div');
        agentMsg.className = 'agent-msg';
        agentMsg.textContent = 'Thank you for your message. We are looking into it.';
        chatBody.appendChild(agentMsg);
        chatBody.scrollTop = chatBody.scrollHeight;
      }, 1500);
    }
  });
  