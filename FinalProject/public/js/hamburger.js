// document.addEventListener('DOMContentLoaded', function () {
//     const openIcon = document.getElementById('openIcon');
//     const closeIcon = document.getElementById('closeIcon');
//     const navBar = document.querySelector('.nav-bar');

//     openIcon.addEventListener('click', () => {
//         navBar.classList.add('active');
//         openIcon.classList.add('hide-mobile');
//         closeIcon.classList.remove('hide-mobile');
//     });

//     closeIcon.addEventListener('click', () => {
//         navBar.classList.remove('active');
//         closeIcon.classList.add('hide-mobile');
//         openIcon.classList.remove('hide-mobile');
//     });

//     // Reset menu if window is resized above 980px
//     window.addEventListener('resize', () => {
//         if (window.innerWidth > 980) {
//             navBar.classList.remove('active');
//             openIcon.classList.remove('hide-mobile');
//             closeIcon.classList.add('hide-mobile');
//         }
//     });
// });