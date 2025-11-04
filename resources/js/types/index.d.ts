export type AuthenticatedPageProps = App.Data.Inertia.AppPagePropsData & {
    user: App.Data.Auth.UserData;
};

interface Link {
    url: string | null;
    label: string;
    page: number | null;
    active: boolean;
}

export type PaginatedCollection<T extends object> = {
    current_page: number;
    data: T[];
    first_page_url: string;
    from: number;
    last_page: number;
    last_page_url: string;
    links: Link[];
    next_page_url: string | null;
    path: string;
    per_page: number;
    prev_page_url: string | null;
    to: number;
    total: number;
};

export interface Stat {
    title: string;
    icon: string;
    value: number | string;
    variation: number;
    formatter?: (value: number) => string;
}
