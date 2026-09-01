import { useTranslation } from 'react-i18next';
import InputError from '@/components/input-error';
import PasswordInput from '@/components/password-input';
import { Button } from '@/components/ui/button';
import { DialogFooter } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import FieldLabel from '@/pages/clients/components/FieldLabel';
import type { RoleOption, UserListItem } from '../../types';

type FormErrors = Partial<
    Record<
        'name' | 'email' | 'password' | 'password_confirmation' | 'role',
        string
    >
>;

type Props = {
    user: UserListItem;
    roles: RoleOption[];
    processing: boolean;
    errors: FormErrors;
    onCancel: () => void;
};

export default function EditUserForm({
    user,
    roles,
    processing,
    errors,
    onCancel,
}: Props) {
    const { t } = useTranslation();

    return (
        <>
            <div className="grid gap-2">
                <FieldLabel htmlFor="edit-user-name" required>
                    {t('common.name')}
                </FieldLabel>
                <Input
                    id="edit-user-name"
                    name="name"
                    required
                    autoFocus
                    defaultValue={user.name}
                    placeholder={t('users.namePlaceholder')}
                />
                <InputError message={errors.name} />
            </div>

            <div className="grid gap-2">
                <FieldLabel htmlFor="edit-user-email" required>
                    {t('common.email')}
                </FieldLabel>
                <Input
                    id="edit-user-email"
                    type="email"
                    name="email"
                    required
                    defaultValue={user.email}
                    placeholder={t('users.emailPlaceholder')}
                />
                <InputError message={errors.email} />
            </div>

            <div className="grid gap-2">
                <FieldLabel htmlFor="edit-user-password">
                    {t('users.newPassword')}
                </FieldLabel>
                <PasswordInput
                    id="edit-user-password"
                    name="password"
                    autoComplete="new-password"
                    placeholder={t('users.newPasswordPlaceholder')}
                />
                <InputError message={errors.password} />
            </div>

            <div className="grid gap-2">
                <FieldLabel htmlFor="edit-user-password_confirmation">
                    {t('users.passwordConfirmation')}
                </FieldLabel>
                <PasswordInput
                    id="edit-user-password_confirmation"
                    name="password_confirmation"
                    autoComplete="new-password"
                    placeholder={t('users.passwordConfirmationPlaceholder')}
                />
                <InputError message={errors.password_confirmation} />
            </div>

            <div className="grid gap-2">
                <FieldLabel htmlFor="edit-user-role" required>
                    {t('users.role')}
                </FieldLabel>
                <select
                    id="edit-user-role"
                    name="role"
                    required
                    defaultValue={user.roles[0] ?? ''}
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

            <DialogFooter>
                <Button type="button" variant="outline" onClick={onCancel}>
                    {t('common.cancel')}
                </Button>
                <Button type="submit" disabled={processing}>
                    {t('common.save')}
                </Button>
            </DialogFooter>
        </>
    );
}
