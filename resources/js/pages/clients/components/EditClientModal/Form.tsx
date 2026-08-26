import { useTranslation } from 'react-i18next';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { DialogFooter } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import type { Client } from '../../types';
import FieldLabel from '../FieldLabel';

type FormErrors = Partial<Record<'name' | 'email' | 'phone' | 'notes', string>>;

type Props = {
    client: Client;
    processing: boolean;
    errors: FormErrors;
    onCancel: () => void;
};

export default function EditClientForm({
    client,
    processing,
    errors,
    onCancel,
}: Props) {
    const { t } = useTranslation();

    return (
        <>
            <div className="grid gap-2">
                <FieldLabel htmlFor="edit-name" required>
                    {t('common.name')}
                </FieldLabel>
                <Input
                    id="edit-name"
                    name="name"
                    required
                    autoFocus
                    defaultValue={client.name}
                    placeholder={t('clients.namePlaceholder')}
                />
                <InputError message={errors.name} />
            </div>

            <div className="grid gap-2">
                <FieldLabel htmlFor="edit-email">
                    {t('common.email')}
                </FieldLabel>
                <Input
                    id="edit-email"
                    type="email"
                    name="email"
                    defaultValue={client.email ?? ''}
                    placeholder={t('clients.emailPlaceholder')}
                />
                <InputError message={errors.email} />
            </div>

            <div className="grid gap-2">
                <FieldLabel htmlFor="edit-phone">
                    {t('common.phone')}
                </FieldLabel>
                <Input
                    id="edit-phone"
                    name="phone"
                    defaultValue={client.phone ?? ''}
                    placeholder={t('clients.phonePlaceholder')}
                />
                <InputError message={errors.phone} />
            </div>

            <div className="grid gap-2">
                <FieldLabel htmlFor="edit-notes">
                    {t('common.notes')}
                </FieldLabel>
                <Textarea
                    id="edit-notes"
                    name="notes"
                    defaultValue={client.notes ?? ''}
                    placeholder={t('clients.notesPlaceholder')}
                />
                <InputError message={errors.notes} />
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
