function previewPhoto(input) {
    const preview = document.getElementById('photo-preview');
    const placeholder = document.getElementById('upload-placeholder');
    const uploadBox = input.parentElement;
    
    // Remove any existing validation message
    const existingMessage = uploadBox.nextElementSibling;
    if (existingMessage && existingMessage.classList.contains('validation-message')) {
        existingMessage.remove();
    }
    
    // Validate file size (2MB max)
    const maxSize = 2 * 1024 * 1024; // 2MB in bytes
    if (input.files[0].size > maxSize) {
        uploadBox.classList.add('is-invalid');
        const message = document.createElement('div');
        message.className = 'validation-message';
        message.textContent = 'File size must be less than 2MB';
        uploadBox.after(message);
        input.value = '';
        preview.style.display = 'none';
        placeholder.style.display = 'block';
        return;
    }

    // Validate file type
    const validTypes = ['image/jpeg', 'image/png'];
    if (!validTypes.includes(input.files[0].type)) {
        uploadBox.classList.add('is-invalid');
        const message = document.createElement('div');
        message.className = 'validation-message';
        message.textContent = 'Please upload a PNG or JPEG file';
        uploadBox.after(message);
        input.value = '';
        preview.style.display = 'none';
        placeholder.style.display = 'block';
        return;
    }

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
            placeholder.style.display = 'none';
            uploadBox.classList.remove('is-invalid');
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}