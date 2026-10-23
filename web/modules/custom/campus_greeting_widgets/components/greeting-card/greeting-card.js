/**
 * @file
 * Botón "Me gusta" de la tarjeta de saludo.
 */
((Drupal, once) => {
  Drupal.behaviors.greetingCardLike = {
    attach(context) {
      once('greeting-like', '[data-greeting-like]', context).forEach((el) => {
        el.addEventListener('click', () => {
          el.classList.toggle('is-liked');
        });
      });
    },
  };
})(Drupal, once);
