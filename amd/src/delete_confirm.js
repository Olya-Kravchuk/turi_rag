import { get_strings } from 'core/str';
import ModalSaveCancel from 'core/modal_save_cancel';
import ModalEvents from 'core/modal_events';
import Notification from 'core/notification';

export const init = (selector) => {
    document.addEventListener('click', (e) => {
        const deleteBtn = e.target.closest(selector);
        if (!deleteBtn) {
            return;
        }

        e.preventDefault();
        const targetUrl = deleteBtn.getAttribute('href');

        Promise.all([
            get_strings([
                { key: 'delete_profile_confirm_title', component: 'local_turi_rag' },
                { key: 'delete_profile_confirm_body', component: 'local_turi_rag' },
                { key: 'delete', component: 'core' }
            ]),
            ModalSaveCancel.create()
        ]).then(([[title, body, deleteText], modal]) => {
            modal.setTitle(title);
            modal.setBody(body);
            modal.setSaveButtonText(deleteText);

            const root = modal.getRoot();
            root.find('.modal-dialog').addClass('modal-dialog-centered');
            root.find('[data-action="save"]').removeClass('btn-primary').addClass('btn-danger');

            root.on(ModalEvents.save, () => {
                window.location.href = targetUrl;
            });

            root.on(ModalEvents.hidden, () => {
                modal.destroy();
            });

            modal.show();
            return modal;
        }).catch(Notification.exception);
    });
};