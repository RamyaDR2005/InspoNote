// File: script.js
function loadNotes() {
  fetch('notes.json')
    .then(res => res.json())
    .then(data => {
      const container = document.getElementById('notesContainer');
      container.innerHTML = '';
      data.reverse().forEach(note => {
        const card = document.createElement('div');
        card.className = 'note-card';

        card.innerHTML = `
          <h3>${note.title}</h3>
          <p>${note.description}</p>
          <div class="timestamp">Created at: ${note.created_at}</div>
          <div class="attachments">
            ${note.attachments.image ? `<img src="${note.attachments.image}" alt="" style="max-width:100px;">` : ''}
            ${note.attachments.audio ? `<audio controls src="${note.attachments.audio}"></audio>` : ''}
            ${note.attachments.file ? `<a href="${note.attachments.file}" target="_blank">📎 Attached File</a>` : ''}
          </div>
          <button class="delete-btn" onclick="deleteNote('${note.id}')">Delete</button>
        `;

        container.appendChild(card);
      });
    })
    .catch(err => {
      console.error('Error loading notes:', err);
    });
}

function deleteNote(id) {
  const formData = new FormData();
  formData.append('delete', id);

  fetch('notes.php', {
    method: 'POST',
    body: formData
  })
    .then(res => res.json())
    .then(response => {
      if (response.status === 'deleted') {
        loadNotes();
      }
    });
}
