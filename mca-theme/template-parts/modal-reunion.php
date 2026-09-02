<?php
/**
 * Modal: solicitud de reunión comercial.
 *
 * @package MCA
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- ==========================================================================
     Modal — solicitud de reunión comercial
     Native <dialog>: focus trapping, Esc to close and an inert background all
     come from the platform. `:target` keeps it usable if the script fails.
     ========================================================================== -->
<dialog class="modal" id="modal-reunion" aria-labelledby="modal-reunion-title">
  <div class="modal__panel">
    <button class="modal__close" type="button" data-modal-close aria-label="Cerrar">
      <span aria-hidden="true">&times;</span>
    </button>

    <header class="modal__head">
      <span class="eyebrow"><?php echo esc_html( mca_option( 'modal_eyebrow' ) ); ?></span>
      <h2 class="modal__title" id="modal-reunion-title"><?php echo esc_html( mca_option( 'modal_title' ) ); ?></h2>
      <p class="modal__lead">
        <?php echo esc_html( mca_option( 'modal_lead' ) ); ?>
      </p>
    </header>

    <?php
    /*
     * The form submits to the REST API endpoint for meeting requests.
     * The endpoint URL is built dynamically using rest_url().
     */
    ?>
    <form class="form" action="<?php echo esc_url( rest_url( 'mca/v1/meeting' ) ); ?>" method="post" data-meeting-form novalidate>
      <div class="form__body">
        <div class="form__row">
          <label class="form__field">
            <span class="form__label">Nombres completos</span>
            <input class="form__input" type="text" name="nombre" autocomplete="name"
                   required maxlength="120" placeholder="Ej. María Fernández">
            <span class="form__error" data-error></span>
          </label>
        </div>

        <div class="form__row form__row--split">
          <label class="form__field">
            <span class="form__label">Correo</span>
            <input class="form__input" type="email" name="correo" autocomplete="email"
                   required maxlength="160" placeholder="nombre@empresa.com">
            <span class="form__error" data-error></span>
          </label>

          <label class="form__field">
            <span class="form__label">Celular</span>
            <input class="form__input" type="tel" name="celular" autocomplete="tel"
                   required inputmode="tel" maxlength="24" placeholder="+51 999 999 999">
            <span class="form__error" data-error></span>
          </label>
        </div>

        <div class="form__row">
          <label class="form__field">
            <span class="form__label">Empresa</span>
            <input class="form__input" type="text" name="empresa" autocomplete="organization"
                   required maxlength="120" placeholder="Nombre de tu organización">
            <span class="form__error" data-error></span>
          </label>
        </div>

        <div class="form__row">
          <label class="form__field">
            <span class="form__label">Cuéntanos tu requerimiento</span>
            <textarea class="form__input form__input--area" name="mensaje" rows="3"
                      required maxlength="1000"
                      placeholder="Qué operación quieres resolver, volúmenes, canales…"></textarea>
            <span class="form__error" data-error></span>
          </label>
        </div>

      </div>

      <p class="form__status" data-form-status role="status" aria-live="polite"></p>

      <div class="form__actions">
        <button class="btn btn--primary" type="submit"><?php echo esc_html( mca_option( 'modal_submit' ) ); ?></button>
        <button class="btn btn--outline" type="button" data-modal-close><?php echo esc_html( mca_option( 'modal_cancel' ) ); ?></button>
      </div>
    </form>
  </div>
</dialog>
