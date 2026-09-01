import { Form, Head, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import UserController from '@/actions/App/Http/Controllers/UserController';
import Heading from '@/components/heading';
import { index as usersIndex, create as usersCreate } from '@/routes/users';
import UserForm from './Form';

export default function UsersCreate() {
    const { t } = useTranslation();
    const { roles = [] } = usePage().props;

    return (
        <>
            <Head title={t('users.createTitle')} />

            <div className="flex h-full flex-1 flex-col gap-6 p-4">
                <Heading
                    title={t('users.createTitle')}
                    description={t('users.createDescription')}
                />

                <Form
                    {...UserController.store.form()}
                    className="max-w-xl space-y-6"
                >
                    {({ processing, errors }) => (
                        <UserForm
                            processing={processing}
                            errors={errors}
                            roles={roles}
                        />
                    )}
                </Form>
            </div>
        </>
    );
}

UsersCreate.layout = {
    breadcrumbs: [
        {
            title: 'Users',
            href: usersIndex(),
        },
        {
            title: 'Add user',
            href: usersCreate(),
        },
    ],
};
