import { router } from '@inertiajs/vue3';
import { useUrlSearchParams, watchDebounced } from '@vueuse/core';
import { useChangeCase } from '@vueuse/integrations/useChangeCase';
import { reactive, ref } from 'vue';

export function useTable(
    baseUrl: string,
    initialPage: number,
    initialFilters: Record<string, any> = { search: '' },
) {
    const params = useUrlSearchParams('history');

    const currentPage = ref(initialPage);
    const sort = ref<string | null>((params.sort as string) || null);

    const filters = reactive<Record<string, any>>({ ...initialFilters });

    for (const key in filters) {
        const paramKey = `filter[${key}]`;
        if (params[paramKey] !== undefined && params[paramKey] !== null) {
            filters[key] = params[paramKey];
        }
    }

    function reloadData() {
        const queryParams: Record<string, any> = {
            page: currentPage.value,
        };

        if (sort.value) {
            const sortKey = sort.value.startsWith('-')
                ? `-${useChangeCase(sort.value.substring(1), 'snakeCase').value}`
                : useChangeCase(sort.value, 'snakeCase').value;
            queryParams.sort = sortKey;
        }

        for (const key in filters) {
            if (filters[key]) {
                queryParams[`filter[${key}]`] = filters[key];
            }
        }

        router.get(
            `${baseUrl}?${new URLSearchParams(queryParams).toString()}`,
            {},
            { preserveState: true, preserveScroll: true },
        );
    }

    function onSort(columnId: string) {
        if (sort.value === columnId) {
            sort.value = `-${columnId}`;
        } else if (sort.value === `-${columnId}`) {
            sort.value = null;
        } else {
            sort.value = columnId;
        }
        currentPage.value = 1;
        reloadData();
    }

    function onPageChange(page: number) {
        currentPage.value = page;
        reloadData();
    }

    watchDebounced(
        filters,
        () => {
            currentPage.value = 1;
            reloadData();
        },
        { debounce: 300, deep: true },
    );

    function syncCurrentPage(newPage: number) {
        currentPage.value = newPage;
    }

    return {
        currentPage,
        sort,
        filters,
        onSort,
        onPageChange,
        syncCurrentPage,
    };
}
