/**
 * Add was-validated to inputs parent iff the input is not empty.
 */
$(document).ready(function () {
    $(".form-control").on('input', function () {
        if (this.value === '') {
            $(this).parent().removeClass('was-validated');
        } else {
            $(this).parent().addClass('was-validated');
        }
    });
});
