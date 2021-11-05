/**
 * Add was-validated to inputs parent iff the input is not empty.
 */
const inputs = document.getElementsByClassName('form-control');
for (const input of inputs) {
  input.onchange = function () {
    if (this.value === '') {
      input.parentElement.classList.remove('was-validated');
    } else {
      input.parentElement.classList.add('was-validated');
    }
  }
}
