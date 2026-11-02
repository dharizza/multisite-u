/**
 * @file
 * Botón "Me gusta" de la tarjeta de saludo.
 */
((Drupal, once) => {
  Drupal.behaviors.greetingCardLike = {
    attach(context) {
      once('greeting-like', '[data-greeting-like]', context).forEach((button) => {
        button.addEventListener('click', () => {
          // Hallazgo A4: el lector de pantalla anuncia el estado.
          const pressed = button.getAttribute('aria-pressed') === 'true';
          button.setAttribute('aria-pressed', String(!pressed));
        });
      });
    },
  };
})(Drupal, once);
