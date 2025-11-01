document.addEventListener('DOMContentLoaded', () => {
  const autoDismiss = document.querySelectorAll('.alert');
  setTimeout(() => autoDismiss.forEach(a => a.classList.add('hide')), 6000);
});

