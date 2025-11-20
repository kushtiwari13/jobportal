document.addEventListener('DOMContentLoaded', () => {
  const autoDismiss = document.querySelectorAll('.alert');
  setTimeout(() => autoDismiss.forEach(a => a.classList.add('hide')), 6000);

  // Auto-submit search when user types (>=3 chars) or clears to empty
  const forms = document.querySelectorAll('.auto-search');
  forms.forEach(form => {
    let timer;
    const inputs = form.querySelectorAll('input[type="text"]');
    const triggerSearch = () => {
      clearTimeout(timer);
      timer = setTimeout(() => {
        const qVal = (form.querySelector('[name="q"]')?.value || '').trim();
        const locVal = (form.querySelector('[name="location"]')?.value || '').trim();
        // Submit after any input; empty values mean show all.
        form.submit();
      }, 350);
    };
    inputs.forEach(input => input.addEventListener('input', triggerSearch));
  });
});
