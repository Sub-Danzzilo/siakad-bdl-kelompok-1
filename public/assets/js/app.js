// Konfirmasi sebelum aksi berbahaya: <form data-confirm="Yakin hapus data ini?">
document.addEventListener('submit', (event) => {
  const pesan = event.target.dataset.confirm;
  if (pesan && !window.confirm(pesan)) {
    event.preventDefault();
  }
});
