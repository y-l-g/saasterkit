declare namespace App {
    namespace Data {
        namespace AppNotifications {
            export type AppNotificationData = {
                readonly id: number;
                readonly title: string;
                readonly body: string;
                readonly createdAt: string;
            };
        }
        namespace Auth {
            export type UserData = {
                readonly id: number;
                readonly name: string;
                readonly email: string;
                readonly isAdmin: boolean;
                readonly createdAt: string;
                readonly emailVerifiedAt: string | null;
                readonly neutralColor: string | null;
                readonly currentTeamId: number | null;
                readonly primaryColor: string | null;
                readonly secondaryColor: string | null;
                hasPassword: boolean;
                readonly currentTeam?: App.Data.Teams.TeamData | null;
                readonly teams?: App.Data.Teams.TeamData[];
                readonly notifications?: App.Data.AppNotifications.AppNotificationData[];
            };
        }
        namespace Billing {
            export type BillingSettingsPageData = {
                readonly plans: App.Data.Billing.PlanData[];
                readonly invoices?: App.Data.Billing.InvoiceData[];
                readonly team: App.Data.Teams.TeamData;
            };
            export type InvoiceData = {
                readonly date: string;
                readonly total: string;
                readonly url: string;
                readonly status: App.Enums.Billing.InvoiceStatusEnum;
                readonly id: string;
                readonly number: string;
            };
            export type PlanData = {
                readonly name: App.Enums.Billing.PlanEnum;
                readonly features: App.Enums.Billing.FeatureEnum[];
                readonly limits: Record<string, number>;
                readonly prices: Record<string, string>;
            };
            export type SubscriptionData = {
                readonly id: number;
                readonly status: App.Enums.Billing.SubscriptionStatusEnum;
                readonly endsAt: string | null;
                readonly onGracePeriod: boolean;
                readonly plan: App.Data.Billing.PlanData;
                readonly stripePriceId: string;
                readonly createdAt: string | null;
                readonly trialEndsAt: string | null;
                readonly team?: App.Data.Teams.TeamData;
                readonly period: App.Enums.Billing.BillingPeriodEnum;
            };
        }
        namespace Inertia {
            export type AppPagePropsData = {
                readonly user?: App.Data.Auth.UserData | null;
                readonly permissions: App.Enums.Teams.TeamMemberPermissionEnum[];
                readonly flash: App.Data.Inertia.FlashData;
            };
            export type FlashData = {
                readonly success?: string | null;
                readonly error?: string | null;
                readonly info?: string | null;
                readonly status?: string | null;
            };
        }
        namespace Teams {
            export type TeamData = {
                readonly id: number;
                readonly userId: number;
                readonly createdAt: string;
                readonly name: string;
                readonly slug: string;
                readonly owner?: App.Data.Teams.TeamMemberData;
                readonly invitations?: App.Data.Teams.TeamInvitationData[];
                readonly subscription?: App.Data.Billing.SubscriptionData | null;
            };
            export type TeamInvitationData = {
                readonly id: number;
                readonly email: string;
                readonly role: App.Enums.Teams.RoleEnum | null;
                readonly team?: App.Data.Teams.TeamData | null;
            };
            export type TeamMemberData = {
                readonly id: number;
                readonly name: string;
                readonly email: string;
                readonly role?: App.Enums.Teams.RoleEnum | null;
            };
            export type TeamRoleData = {
                readonly key: App.Enums.Teams.RoleEnum;
                readonly name: string;
                readonly permissions: App.Enums.Teams.TeamMemberPermissionEnum[];
                readonly description: string;
            };
            export type UserTeamIndexData = {
                readonly id: number;
                readonly name: string;
                readonly slug: string;
                readonly isOwner: boolean;
                readonly isCurrentTeam: boolean;
            };
        }
    }
    namespace Enums {
        namespace Auth {
            export type AuthEnum =
                | 'accept-team-ownership-invitation'
                | 'accept-team-invitation'
                | 'send-app-notification';
            export type FortifyStatusEnum =
                | 'password-updated'
                | 'profile-information-updated'
                | 'two-factor-authentication-confirmed'
                | 'two-factor-authentication-disabled'
                | 'two-factor-authentication-enabled'
                | 'verification-link-sent'
                | 'recovery-codes-generated';
            export type SocialiteProviderEnum = 'google' | 'github';
        }
        namespace Billing {
            export type BillingPeriodEnum = 'month' | 'year';
            export type FeatureEnum =
                | 'pro_feature1'
                | 'pro_feature2'
                | 'pro_feature3'
                | 'premium_feature1'
                | 'premium_feature2'
                | 'premium_feature3';
            export type InvoiceStatusEnum = 'paid' | 'draft' | 'open';
            export type PlanEnum = 'pro' | 'premium';
            export type SubscriptionStatusEnum =
                | 'active'
                | 'canceled'
                | 'incomplete'
                | 'incomplete_expired'
                | 'past_due'
                | 'paused'
                | 'trialing'
                | 'unpaid';
        }
        namespace Settings {
            export type ColorEnum =
                | 'gray'
                | 'red'
                | 'orange'
                | 'amber'
                | 'yellow'
                | 'lime'
                | 'green'
                | 'emerald'
                | 'teal'
                | 'cyan'
                | 'sky'
                | 'blue'
                | 'indigo'
                | 'violet'
                | 'purple'
                | 'fuchsia'
                | 'pink'
                | 'rose'
                | 'slate'
                | 'zinc'
                | 'stone'
                | 'old-neutral';
        }
        namespace Teams {
            export type RoleEnum = 'admin' | 'editor';
            export type TeamMemberPermissionEnum =
                | 'team.view'
                | 'team.update'
                | 'team.delete'
                | 'team.owner.transfer'
                | 'team.member.invite'
                | 'team.member.update'
                | 'team.member.remove'
                | 'team.invitation.cancel'
                | 'billing.portal.view'
                | 'billing.settings.view'
                | 'billing.checkout.create';
        }
    }
}
declare namespace Illuminate {
    export type CursorPaginator<TKey, TValue> = {
        data: TKey extends string ? Record<TKey, TValue> : TValue[];
        links: {
            url: string | null;
            label: string;
            active: boolean;
        }[];
        meta: {
            path: string;
            per_page: number;
            next_cursor: string | null;
            next_page_url: string | null;
            prev_cursor: string | null;
            prev_page_url: string | null;
        };
    };
    export type CursorPaginatorInterface<TKey, TValue> =
        Illuminate.CursorPaginator<TKey, TValue>;
    export type LengthAwarePaginator<TKey, TValue> = {
        data: TKey extends string ? Record<TKey, TValue> : TValue[];
        links: {
            url: string | null;
            label: string;
            active: boolean;
        }[];
        meta: {
            total: number;
            current_page: number;
            first_page_url: string;
            from: number | null;
            last_page: number;
            last_page_url: string;
            next_page_url: string | null;
            path: string;
            per_page: number;
            prev_page_url: string | null;
            to: number | null;
        };
    };
    export type LengthAwarePaginatorInterface<TKey, TValue> =
        Illuminate.LengthAwarePaginator<TKey, TValue>;
}
declare namespace Spatie {
    namespace LaravelData {
        export type CursorPaginatedDataCollection<TKey, TValue> =
            Illuminate.CursorPaginator<TKey, TValue>;
        export type PaginatedDataCollection<TKey, TValue> =
            Illuminate.LengthAwarePaginator<TKey, TValue>;
    }
}
