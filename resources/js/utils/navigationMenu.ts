import type { DropdownMenuItem, NavigationMenuItem } from '@nuxt/ui';

const toDropdownMenuItem = (item: NavigationMenuItem): DropdownMenuItem => {
    const dropdownItem = { ...item } as DropdownMenuItem & {
        children?: NavigationMenuItem['children'];
        type?: NavigationMenuItem['type'];
        ui?: NavigationMenuItem['ui'];
    };
    delete dropdownItem.children;
    delete dropdownItem.type;
    delete dropdownItem.ui;

    return {
        ...dropdownItem,
        children: item.children?.map((child) =>
            toDropdownMenuItem(child as NavigationMenuItem),
        ),
    };
};

export const toDropdownMenuItems = (
    items: NavigationMenuItem[][],
): DropdownMenuItem[][] =>
    items.map((group) => group.map((item) => toDropdownMenuItem(item)));
