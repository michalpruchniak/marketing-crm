import { Link } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import InputError from '@/components/input-error';
import PasswordInput from '@/components/password-input';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { index as usersIndex } from '@/routes/users';
import FieldLabel from '../clients/components/FieldLabel';
import type { RoleOption } from './types';

type FormErrors = Partial<
    Record<
        'name' | 'email' | 'password' | 'password_confirmation' | 'role',
        string
    >
>;

type Props = {
    processing: boolean;
    errors: FormErrors;
    roles: RoleOption[];
    defaultName?: string;
    defaultEmail?: string;
    defaultRole?: string;
    requirePassword?: boolean;
    idPrefix?: string;
};

export default function UserForm({
    processing,
    errors,
    roles,
    defaultName = '',
    defaultEmail = '',
    defaultRole = '',
    requirePassword = true,
    idPrefix = '',
}: Props) {
    const { t } = useTranslation();

    return (
        <>
            <div className="grid gap-2">
                <FieldLabel htmlFor={`${idPrefix}name`} required>
                    {t('common.name')}
                </FieldLabel>
                <Input
                    id={`${idPrefix}name`}
                    name="name"
                    required
                    autoFocus={idPrefix === ''}
                    defaultValue={defaultName}
                    placeholder={t('users.namePlaceholder')}
                />
                <InputError message={errors.name} />
            </div>

            <div className="grid gap-2">
                <FieldLabel htmlFor={`${idPrefix}email`} required>
                    {t('common.email')}
                </FieldLabel>
                <Input
                    id={`${idPrefix}email`}
                    type="email"
                    name="email"
                    required
                    defaultValue={defaultEmail}
                    placeholder={t('users.emailPlaceholder')}
                />
                <InputError message={errors.email} />
            </div>

            <div className="grid gap-2">
                <FieldLabel
                    htmlFor={`${idPrefix}password`}
                    required={requirePassword}
                >
                    {requirePassword
                        ? t('common.password')
                        : t('users.newPassword')}
                </FieldLabel>
                <PasswordInput
                    id={`${idPrefix}password`}
                    name="password"
                    required={requirePassword}
                    autoComplete="new-password"
                    placeholder={
                        requirePassword
                            ? t('users.passwordPlaceholder')
                            : t('users.newPasswordPlaceholder')
                    }
                />
                <InputError message={errors.password} />
            </div>

            <div className="grid gap-2">
                <FieldLabel
                    htmlFor={`${idPrefix}password_confirmation`}
                    required={requirePassword}
                >
                    {t('users.passwordConfirmation')}
                </FieldLabel>
                <PasswordInput
                    id={`${idPrefix}password_confirmation`}
                    name="password_confirmation"
                    required={requirePassword}
                    autoComplete="new-password"
                    placeholder={
                        requirePassword
                            ? t('users.passwordConfirmationPlaceholder')
                            : t('users.newPasswordPlaceholder')
                    }
                />
                <InputError message={errors.password_confirmation} />
            </div>

            <div className="grid gap-2">
                <FieldLabel htmlFor={`${idPrefix}role`} required>
                    {t('users.role')}
                </FieldLabel>
                <select
                    id={`${idPrefix}role`}
                    name="role"
                    required
                    defaultValue={defaultRole}
                    className="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50"
                >
                    <option value="" disabled>
                        {t('users.rolePlaceholder')}
                    </option>
                    {roles.map((role) => (
                        <option key={role.value} value={role.value}>
                            {role.label}
                        </option>
                    ))}
                </select>
                <InputError message={errors.role} />
            </div>

            <div className="flex gap-3">
                <Button type="submit" disabled={processing}>
                    {t('common.save')}
                </Button>
                <Button variant="outline" asChild>
                    <Link href={usersIndex()}>{t('common.cancel')}</Link>
                </Button>
            </div>
        </>
    );
}
