import { Form, Head, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import UserController from '@/actions/App/Http/Controllers/UserController';
import Heading from '@/components/heading';
import i18n from '@/i18n';
import { index as usersIndex } from '@/routes/users';
import UserForm from './Form';
import type { UserFormData } from './types';

export default function UsersEdit({ user }: { user: UserFormData }) {
    const { t } = useTranslation();
    const { roles = [] } = usePage().props;

    return (
        <>
            <Head title={t('users.editTitle')} />

            <div className="flex h-full flex-1 flex-col gap-6 p-4">
                <Heading
                    title={t('users.editTitle')}
                    description={t('users.editDescription')}
                />

                <Form
                    {...UserController.update.form(user.id)}
                    className="max-w-xl space-y-6"
                >
                    {({ processing, errors }) => (
                        <UserForm
                            processing={processing}
                            errors={errors}
                            roles={roles}
                            defaultName={user.name}
                            defaultEmail={user.email}
                            defaultRole={user.role}
                            requirePassword={false}
                        />
                    )}
                </Form>
            </div>
        </>
    );
}

UsersEdit.layout = {
    breadcrumbs: [
        {
            title: i18n.t('users.pageTitle'),
            href: usersIndex(),
        },
        {
            title: i18n.t('users.editTitle'),
            href: '#',
        },
    ],
};
