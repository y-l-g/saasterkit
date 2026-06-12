import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import test from 'node:test';

const twoFactorSetupModal = readFileSync(
    new URL(
        '../../resources/js/components/settings/TwoFactorSetupModal.vue',
        import.meta.url,
    ),
    'utf8',
);

test('two factor setup reads confirmation errors without unsafe casts', () => {
    assert.match(twoFactorSetupModal, /function getConfirmationCodeError/);
    assert.match(twoFactorSetupModal, /Record<string, unknown>/);
    assert.match(twoFactorSetupModal, /typeof errors\.code === 'string'/);
    assert.match(twoFactorSetupModal, /confirmationError\.code/);
    assert.match(
        twoFactorSetupModal,
        /:error="getConfirmationCodeError\(errors\)"/,
    );
    assert.doesNotMatch(twoFactorSetupModal, /as any/);
});
