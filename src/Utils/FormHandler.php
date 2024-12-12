<?php

namespace jidaikobo\kontiki\Utils;

use DOMDocument;

class FormHandler
{
    private DOMDocument $dom;

    /**
     * Initialize the DOM helper with HTML content.
     *
     * @param string $html The HTML content to parse.
     */
    public function __construct(string $html = '')
    {
        $this->dom = new DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);
        if (!empty($html)) {
            $this->loadHTML($html);
        }
    }

    public function loadHTML(string $html): void
    {
        $this->dom->loadHTML(
            mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'),
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
    }

    /**
     * Validate the name and id attributes for form elements.
     *
     * @throws \Exception if validation errors are found.
     * @return void
     */
    public function validateNameIdMapping(): void
    {
        $errors = [];
        $elements = ['input', 'textarea', 'select', 'button', 'fieldset', 'label'];

        foreach ($elements as $tag) {
            $nodes = $this->dom->getElementsByTagName($tag);
            foreach ($nodes as $node) {
                $name = $node->getAttribute('name');
                $id = $node->getAttribute('id');

                // Special handling for <label>
                if ($tag === 'label') {
                    $for = $node->getAttribute('for');
                    if ($for && !$this->dom->getElementById($for)) {
                        $errors[] = sprintf("Label 'for' attribute references a non-existent input ID: '%s'.", $for);
                    }
                    continue;
                }

                // Missing name or id
                if (!$name || !$id) {
                    $errors[] = sprintf("<%s> element is missing a 'name' or 'id' attribute.", $tag);
                    continue;
                }

                // Mismatch between name and id
                if ($this->nameToId($name) !== $id) {
                    $errors[] = sprintf("Mismatch in <%s>: name='%s' and id='%s' are not consistent.", $tag, $name, $id);
                }
            }
        }

        // Throw exception if errors are found
        if (!empty($errors)) {
            throw new \Exception("Form validation failed:\n" . implode("\n", $errors));
        }
    }

    /**
     * Set an attribute for an element by its ID.
     *
     * @param  string $id        The ID of the element.
     * @param  string $attribute The attribute name.
     * @param  string $value     The attribute value.
     * @return void
     */
    public function setAttributeById(string $id, string $attribute, string $value): void
    {
        $element = $this->dom->getElementById($id);
        if ($element) {
            $element->setAttribute($attribute, $value);
        }
    }

    /**
     * Add a CSS class to an element by its ID.
     *
     * @param  string $id    The ID of the element.
     * @param  string $class The CSS class to add.
     * @return void
     */
    public function addClassById(string $id, string $class): void
    {
        $element = $this->dom->getElementById($id);
        if ($element) {
            $currentClass = $element->getAttribute('class') ?: '';
            $classes = array_unique(array_filter(array_merge(explode(' ', $currentClass), [$class])));
            $element->setAttribute('class', implode(' ', $classes));
        }
    }

    /**
     * Add error messages to form elements based on their name attributes.
     *
     * @param  array $errors Associative array where the key is the form element's name
     *                       and the value is an array of error messages.
     * @return void
     */
    public function addErrors(array $errors): void
    {
        foreach ($errors as $field => $messages) {
            $id = $this->nameToId($field);

            // Add ARIA attributes and classes to the element
            $this->setAttributeById($id, 'aria-invalid', 'true');
            $this->setAttributeById($id, 'aria-errormessage', 'errormessage_' . $id);
            $this->addClassById($id, 'is-invalid');
        }
        // Append error messages
        $this->addErrorSummary($errors);
    }

    /**
     * Add a summary of error messages to the form as the first child element.
     *
     * The summary includes a list of errors, each linking to the corresponding input field.
     * If the name is "0", it is treated as a form-wide error.
     *
     * @param  array $errors Associative array where the key is the form element's name
     *                       and the value is an array of error messages.
     * @return void
     */
    public function addErrorSummary(array $errors): void
    {
        // Wrapper for the error summary (used for AJAX or general styling)
        $wrapper = $this->dom->createElement('div');
        $wrapper->setAttribute('class', 'errormessages');

        // Create the summary <ul> element
        $summary = $this->dom->createElement('ul');
        $summary->setAttribute('class', 'alert alert-danger p-3 ps-5 pt-0 mt-3 mb-3 fs-6');

        foreach ($errors as $field => $messages) {
            // Handle form-wide errors (when name is "0")
            if ($field === 0) {
                $li = $this->dom->createElement('li', Lang::get('found_the_problem', 'Found the problem'));
                $li->setAttribute('class', 'pt-3');

                // Nested <ul> for form-wide error messages
                $nestedUl = $this->dom->createElement('ul');
                $nestedUl->setAttribute('class', 'ps-3');
                foreach ($messages as $message) {
                    $nestedLi = $this->dom->createElement('li', $message);
                    $nestedLi->setAttribute('class', 'pt-2');
                    $nestedUl->appendChild($nestedLi);
                }
                $li->appendChild($nestedUl);

                // Append the form-wide error to the main summary
                $summary->appendChild($li);
                continue;
            }

            // Handle field-specific errors
            $id = $this->nameToId($field);
            $inputElement = $this->dom->getElementById($id);

            // Find the corresponding <label> element
            $labelText = '';
            $labels = $this->dom->getElementsByTagName('label');
            foreach ($labels as $label) {
                if ($label->getAttribute('for') === $id) {
                    $labelText = $label->nodeValue;
                    break;
                }
            }

            // Fallback for cases where no <label> is found
            if (empty($labelText)) {
                $labelText = ucfirst($field);
            }

            // Create the main <li> element for this field
            $li = $this->dom->createElement('li');
            $li->setAttribute('id', 'errormessage_' . $id);
            $li->setAttribute('class', 'pt-3');

            // Add the main error message with a link to the field
            $link = $this->dom->createElement('a', sprintf(Lang::get('error_at_label', 'Error at %s'), $labelText));
            $link->setAttribute('href', "#{$id}");
            $li->appendChild($link);

            // Add a nested <ul> with all specific error messages
            $nestedUl = $this->dom->createElement('ul');
            $nestedUl->setAttribute('class', 'ps-3');
            foreach ($messages as $message) {
                $nestedLi = $this->dom->createElement('li', $labelText . $message);
                $nestedLi->setAttribute('class', 'pt-2');
                $nestedUl->appendChild($nestedLi);
            }
            $li->appendChild($nestedUl);

            // Append this <li> to the main summary <ul>
            $summary->appendChild($li);
        }

        // Wrap the summary and prepend it to the form
        $wrapper->appendChild($summary);

        $form = $this->dom->getElementsByTagName('form')->item(0);
        if ($form) {
            $form->insertBefore($wrapper, $form->firstChild);
        }
    }

    /**
     * Get the modified HTML as a string.
     *
     * @return string The HTML content.
     */
    public function getHtml(): string
    {
        return $this->dom->saveHTML();
    }

    /**
     * Convert a name attribute to an id-compatible string.
     *
     * @param  string $name The name attribute.
     * @return string The converted id.
     */
    public function nameToId(string $name): string
    {
        return preg_replace('/[^a-zA-Z0-9_\-]/', '_', $name);
    }
}
