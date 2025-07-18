/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['modal', 'parent', 'csrfToken'];

    connect() {
        this.element.addEventListener('sylius_admin:taxon:open_delete_modal', (event) => {
            this.csrfTokenTarget.value = event.detail.csrfToken;
            this.modalElement = this.modalTarget;

            this.modalElement.closest('[data-modal-delete-taxon-target]').appendChild(this.modalElement);
            this.modal = new window.bootstrap.Modal(this.modalElement);
            this.modal.show();

            this.modalElement.addEventListener(
                'hidden.bs.modal',
                () => {this.parentTarget.appendChild(this.modalElement);},
                {once: true}
            );
        });
    }
}
