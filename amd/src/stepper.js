export const init = () => {

    const stepper = document.querySelector('.turi-rag-stepper');

    if (!stepper) {
        return;
    }

    const steps = stepper.querySelectorAll('.mdl-stepper-step');

    const updateSteps = () => {
        const currentStep = parseInt(
            stepper.dataset.currentStep,
            10
        );

        if (!currentStep) {
            return;
        }

        steps.forEach((step, index) => {
            const stepNumber = index + 1;

            step.classList.remove(
                'active-step',
                'step-done'
            );

            if (stepNumber < currentStep) {
                step.classList.add('step-done');
            } else if (stepNumber === currentStep) {
                step.classList.add('active-step');
            }
        });
    };

    updateSteps();

    const documentCheckboxes = document.querySelectorAll(
        'input[name="documents[]"]'
    );

    const updateDocumentStep = () => {
        const currentStep = parseInt(
            stepper.dataset.currentStep,
            10
        );

        const hasSelectedDocument = Array.from(documentCheckboxes)
            .some((input) => input.checked);

        const documentsStep = steps[1];
        const profileStep = steps[2];

        if (!documentsStep || !profileStep) {
            return;
        }

        if (currentStep === 2 && hasSelectedDocument) {
            documentsStep.classList.remove('active-step');
            documentsStep.classList.add('step-done');

            profileStep.classList.add('active-step');
        }

        if (currentStep === 2 && !hasSelectedDocument) {
            updateSteps();
        }
    };

    documentCheckboxes.forEach((checkbox) => {
        checkbox.addEventListener('change', updateDocumentStep);
    });

    updateDocumentStep();

    const systemPromptRadios = document.querySelectorAll(
        'input[name="systemprompt"]'
    );

    const updatePromptStep = () => {
        const currentStep = parseInt(
            stepper.dataset.currentStep,
            10
        );

        const hasSelectedPrompt = Array.from(systemPromptRadios)
            .some((input) => input.checked);

        const promptStep = steps[3];
        const resultStep = steps[4];

        if (!promptStep || !resultStep) {
            return;
        }

        if (currentStep === 4 && hasSelectedPrompt) {
            promptStep.classList.remove('active-step');
            promptStep.classList.add('step-done');

            resultStep.classList.add('active-step');
        }

        if (currentStep === 4 && !hasSelectedPrompt) {
            updateSteps();
        }
    };

    systemPromptRadios.forEach((radio) => {
        radio.addEventListener('change', updatePromptStep);
    });

    updatePromptStep();
};