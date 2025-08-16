/**
 * File Utils
 */
class KontikiFileUtils {
    /**
     * Close the modal and (optionally) run a callback after it's fully hidden.
     * @param {string} modalSelectorStr - Modal id or "#id"
     * @param {Function} [onHidden] - Callback executed after hidden.bs.modal
     */
    closeModal(modalSelectorStr, onHidden) {
        // Normalize: allow both "myModal" and "#myModal"
        const modalId = modalSelectorStr.startsWith('#')
            ? modalSelectorStr.slice(1)
            : modalSelectorStr;

        const modalElement = document.getElementById(modalId);
        if (!modalElement) return;

        const modalInstance = bootstrap.Modal.getInstance(modalElement);
        if (!modalInstance) return;

        if (typeof onHidden === 'function') {
            // Run once after the modal is completely hidden
            modalElement.addEventListener('hidden.bs.modal', () => {
                onHidden();
            }, { once: true });
        }

        modalInstance.hide();
    }

    /**
     * Insert the given text at the current caret position of the target textarea.
     * Returns the new caret position.
     * @param {string} targetFieldIdStr
     * @param {string} textToInsert
     * @returns {number|undefined} caret position after insertion
     */
    insertAtCaret(targetFieldIdStr, textToInsert) {
        const targetField = document.getElementById(targetFieldIdStr);
        if (!targetField) return;

        const startPos = targetField.selectionStart ?? targetField.value.length;
        const endPos = targetField.selectionEnd ?? targetField.value.length;
        const textBefore = targetField.value.substring(0, startPos);
        const textAfter = targetField.value.substring(endPos);

        targetField.value = textBefore + textToInsert + textAfter;

        const newCaret = startPos + textToInsert.length;
        targetField.selectionStart = targetField.selectionEnd = newCaret;

        // Do not focus here; focus after modal fully hides.
        return newCaret;
    }
}
