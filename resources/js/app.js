import Alpine from 'alpinejs';
import Quill from 'quill';
import 'quill/dist/quill.snow.css';
import '../css/app.css';

// Initialize Alpine.js BEFORE starting
window.Alpine = Alpine;

// Initialize Quill Editor
window.initQuillEditor = function(elementId, hiddenInputId) {
  const quill = new Quill(`#${elementId}`, {
    theme: 'snow',
    modules: {
      toolbar: [
        [{ 'header': [1, 2, 3, false] }],
        ['bold', 'italic', 'underline', 'strike'],
        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
        [{ 'color': [] }, { 'background': [] }],
        ['link'],
        ['clean']
      ]
    },
    placeholder: 'Write your message here...'
  });

  // Sync with hidden input
  quill.on('text-change', function() {
    const html = quill.root.innerHTML;
    document.getElementById(hiddenInputId).value = html;
  });

  return quill;
};

// Auto-dismiss alerts
document.addEventListener('DOMContentLoaded', function() {
  const alerts = document.querySelectorAll('[data-alert]');
  alerts.forEach(alert => {
    setTimeout(() => {
      alert.classList.add('alert-exit');
      setTimeout(() => alert.remove(), 300);
    }, 5000);
  });
});

// File upload preview
window.previewFiles = function() {
  return {
    files: [],
    handleFiles(event) {
      const fileList = event.target.files;
      this.files = [];
      
      for (let i = 0; i < fileList.length; i++) {
        const file = fileList[i];
        if (file.size > 5242880) { // 5MB
          alert(`File ${file.name} is too large. Max 5MB.`);
          continue;
        }
        this.files.push({
          name: file.name,
          size: this.formatBytes(file.size),
          type: file.type
        });
      }
    },
    removeFile(index) {
      this.files.splice(index, 1);
      // Reset file input
      const input = document.getElementById('attachments');
      const dt = new DataTransfer();
      const fileList = input.files;
      
      for (let i = 0; i < fileList.length; i++) {
        if (i !== index) {
          dt.items.add(fileList[i]);
        }
      }
      input.files = dt.files;
    },
    formatBytes(bytes) {
      if (bytes === 0) return '0 Bytes';
      const k = 1024;
      const sizes = ['Bytes', 'KB', 'MB'];
      const i = Math.floor(Math.log(bytes) / Math.log(k));
      return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    }
  };
};

// Confirm dialog
window.confirmAction = function(message) {
  return confirm(message);
};

// Start Alpine.js after everything is defined
Alpine.start();