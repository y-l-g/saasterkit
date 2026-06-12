import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import test from 'node:test';

const sources = {
    appLayout: readFileSync(
        new URL('../../resources/js/layouts/AppLayout.vue', import.meta.url),
        'utf8',
    ),
    billing: readFileSync(
        new URL(
            '../../resources/js/pages/settings/Billing.vue',
            import.meta.url,
        ),
        'utf8',
    ),
    publicLayout: readFileSync(
        new URL('../../resources/js/layouts/PublicLayout.vue', import.meta.url),
        'utf8',
    ),
    teamLeave: readFileSync(
        new URL(
            '../../resources/js/components/teams/TeamLeaveModal.vue',
            import.meta.url,
        ),
        'utf8',
    ),
    teamRemoveMember: readFileSync(
        new URL(
            '../../resources/js/components/teams/TeamRemoveMemberModal.vue',
            import.meta.url,
        ),
        'utf8',
    ),
    twoFactorSetup: readFileSync(
        new URL(
            '../../resources/js/components/settings/TwoFactorSetupModal.vue',
            import.meta.url,
        ),
        'utf8',
    ),
};

test('template icon-only action buttons have accessible names', () => {
    assert.match(
        sources.appLayout,
        /aria-label="Open notifications"[\s\S]*name="i-lucide-bell"/,
    );
    assert.match(
        sources.appLayout,
        /icon="i-lucide-shield"[\s\S]*aria-label="Admin dashboard"/,
    );
    assert.match(
        sources.publicLayout,
        /:icon="ILucideGithub"[\s\S]*aria-label="GitHub"/,
    );
    assert.match(
        sources.teamLeave,
        /icon="i-lucide-log-out"[\s\S]*aria-label="Leave team"/,
    );
    assert.match(
        sources.teamRemoveMember,
        /icon="i-lucide-log-out"[\s\S]*aria-label="Remove team member"/,
    );
    assert.match(
        sources.twoFactorSetup,
        /:icon="[\s\S]*copied \? 'i-lucide-check' : 'i-lucide-copy'[\s\S]*"[\s\S]*:aria-label=/,
    );
});

test('rendered icon-only action buttons have accessible names', () => {
    assert.match(
        sources.billing,
        /icon: 'i-lucide-download'[\s\S]*'aria-label': `Download invoice/,
    );
});
