/*=============== SHOW HIDDEN - PASSWORD ===============*/
const showHiddenPassRegister = () => {
    const input = document.getElementById('register-pass');
    const iconEye = document.getElementById('register-eye');
 
    iconEye.addEventListener('click', () => {
       if (input.type === 'password') {
          input.type = 'text';
          iconEye.classList.add('ri-eye-line');
          iconEye.classList.remove('ri-eye-off-line');
       } else {
          input.type = 'password';
          iconEye.classList.remove('ri-eye-line');
          iconEye.classList.add('ri-eye-off-line');
       }
    });
 }
 
 showHiddenPassRegister();

 const showHiddenPassConfirm = () => {
    const input = document.getElementById('confirm-pass');
    const iconEye = document.getElementById('confirm-eye');
 
    iconEye.addEventListener('click', () => {
       if (input.type === 'password') {
          input.type = 'text';
          iconEye.classList.add('ri-eye-line');
          iconEye.classList.remove('ri-eye-off-line');
       } else {
          input.type = 'password';
          iconEye.classList.remove('ri-eye-line');
          iconEye.classList.add('ri-eye-off-line');
       }
    });
 }
 
 showHiddenPassConfirm();