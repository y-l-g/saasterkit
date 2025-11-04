import { useAuthPage } from './useAuthPage';

export function useTeamPermissions() {
    const hasTeamPermission = (
        permission: App.Enums.Teams.TeamMemberPermissionEnum,
    ): boolean => {
        const permissions = useAuthPage().props.permissions;
        return permissions.includes(permission);
    };
    return {
        hasTeamPermission,
    };
}
