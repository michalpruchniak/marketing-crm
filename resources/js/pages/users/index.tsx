import { Head, Link, router, usePage } from '@inertiajs/react';
import { Ban, Pencil, Plus, ShieldCheck, Trash2, UserCog } from 'lucide-react';
import { useState } from 'react';
import { useTranslation } from 'react-i18next';
import UserController from '@/actions/App/Http/Controllers/UserController';
import DeleteModal from '@/components/delete-modal';
import Heading from '@/components/heading';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { cn } from '@/lib/utils';
import i18n from '@/i18n';
import { create as usersCreate, index as usersIndex } from '@/routes/users';
import type { UserListItem } from './types';

export default function UsersIndex({ users }: { users: UserListItem[] }) {
    const { t } = useTranslation();
    const { auth, can } = usePage().props;
    const currentUserId = auth.user?.id ?? null;
    const [deleteUser, setDeleteUser] = useState<UserListItem | null>(null);

    function confirmDelete() {
        if (!deleteUser) {
            return;
        }

        router.delete(UserController.destroy.url(deleteUser.id), {
            preserveScroll: true,
        });
    }

    function toggleBan(user: UserListItem) {
        if (user.banned_at) {
            router.delete(UserController.unban.url(user.id), {
                preserveScroll: true,
            });

            return;
        }

        router.patch(
            UserController.ban.url(user.id),
            {},
            {
                preserveScroll: true,
            },
        );
    }

    return (
        <>
            <Head title={t('users.pageTitle')} />

            <div className="flex h-full flex-1 flex-col gap-6 p-4">
                <div className="flex items-start justify-between gap-4">
                    <Heading
                        title={t('users.pageTitle')}
                        description={t('users.pageDescription')}
                    />

                    {can.canUsersCreate && (
                        <Button asChild>
                            <Link href={usersCreate()}>
                                <Plus className="size-4" />
                                {t('users.addUser')}
                            </Link>
                        </Button>
                    )}
                </div>

                {users.length === 0 ? (
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <UserCog className="size-5" />
                                {t('users.noUsersTitle')}
                            </CardTitle>
                            <CardDescription>
                                {t('users.noUsersDescription')}
                            </CardDescription>
                        </CardHeader>
                        {can.canUsersCreate && (
                            <CardContent>
                                <Button asChild>
                                    <Link href={usersCreate()}>
                                        {t('users.addUser')}
                                    </Link>
                                </Button>
                            </CardContent>
                        )}
                    </Card>
                ) : (
                    <div className="overflow-hidden rounded-xl border">
                        <table className="w-full text-left text-sm">
                            <thead className="border-b bg-muted/40">
                                <tr>
                                    <th className="px-4 py-3 font-medium">
                                        {t('common.name')}
                                    </th>
                                    <th className="px-4 py-3 font-medium">
                                        {t('common.email')}
                                    </th>
                                    <th className="px-4 py-3 font-medium">
                                        {t('users.role')}
                                    </th>
                                    <th className="px-4 py-3 font-medium">
                                        {t('users.status')}
                                    </th>
                                    <th className="px-4 py-3 font-medium" />
                                </tr>
                            </thead>
                            <tbody>
                                {users.map((user) => {
                                    const isSelf =
                                        currentUserId !== null &&
                                        user.id === currentUserId;

                                    return (
                                        <tr
                                            key={user.id}
                                            className={cn(
                                                'border-b last:border-0',
                                                user.banned_at &&
                                                    'bg-red-50/70 dark:bg-red-950/20',
                                            )}
                                        >
                                            <td className="px-4 py-3 font-medium">
                                                {user.name}
                                            </td>
                                            <td className="px-4 py-3 text-muted-foreground">
                                                {user.email}
                                            </td>
                                            <td className="px-4 py-3 text-muted-foreground">
                                                {user.roles[0] ?? '—'}
                                            </td>
                                            <td className="px-4 py-3 text-muted-foreground">
                                                {user.banned_at
                                                    ? t('users.banned')
                                                    : t('users.active')}
                                            </td>
                                            <td className="px-4 py-3">
                                                <div className="flex justify-end gap-2">
                                                    {can.canUsersUpdate && (
                                                        <Button
                                                            asChild
                                                            variant="outline"
                                                            size="sm"
                                                        >
                                                            <Link
                                                                href={UserController.edit.url(
                                                                    user.id,
                                                                )}
                                                            >
                                                                <Pencil className="size-4" />
                                                                {t(
                                                                    'common.edit',
                                                                )}
                                                            </Link>
                                                        </Button>
                                                    )}
                                                    {can.canUsersBan &&
                                                        !isSelf && (
                                                            <Button
                                                                type="button"
                                                                variant="outline"
                                                                size="sm"
                                                                onClick={() =>
                                                                    toggleBan(
                                                                        user,
                                                                    )
                                                                }
                                                            >
                                                                {user.banned_at ? (
                                                                    <>
                                                                        <ShieldCheck className="size-4" />
                                                                        {t(
                                                                            'users.unban',
                                                                        )}
                                                                    </>
                                                                ) : (
                                                                    <>
                                                                        <Ban className="size-4" />
                                                                        {t(
                                                                            'users.ban',
                                                                        )}
                                                                    </>
                                                                )}
                                                            </Button>
                                                        )}
                                                    {can.canUsersDelete &&
                                                        !isSelf && (
                                                            <Button
                                                                type="button"
                                                                variant="ghost"
                                                                size="sm"
                                                                onClick={() =>
                                                                    setDeleteUser(
                                                                        user,
                                                                    )
                                                                }
                                                            >
                                                                <Trash2 className="size-4" />
                                                            </Button>
                                                        )}
                                                </div>
                                            </td>
                                        </tr>
                                    );
                                })}
                            </tbody>
                        </table>
                    </div>
                )}
            </div>

            {deleteUser && can.canUsersDelete && (
                <DeleteModal
                    open={deleteUser !== null}
                    onOpenChange={(open) => {
                        if (!open) {
                            setDeleteUser(null);
                        }
                    }}
                    title={t('users.deleteConfirmTitle')}
                    description={t('users.deleteConfirmDescription', {
                        name: deleteUser.name,
                    })}
                    onConfirm={confirmDelete}
                />
            )}
        </>
    );
}

UsersIndex.layout = {
    breadcrumbs: [
        {
            title: i18n.t('users.pageTitle'),
            href: usersIndex(),
        },
    ],
};
