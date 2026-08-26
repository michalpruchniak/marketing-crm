import { useTranslation } from 'react-i18next';
import InputError from '@/components/input-error';
import PasswordInput from '@/components/password-input';
import { Button } from '@/components/ui/button';
import { DialogFooter } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import EncryptedFieldsFrame from '../EncryptedFieldsFrame';
import FieldLabel from '../FieldLabel';

type FormErrors = Partial<
    Record<
        | 'name'
        | 'description'
        | 'url'
        | 'login'
        | 'password'
        | 'additional_information',
        string
    >
>;

type Props = {
    processing: boolean;
    errors: FormErrors;
    onCancel: () => void;
};

export default function AddCredentialForm({
    processing,
    errors,
    onCancel,
}: Props) {
    const { t } = useTranslation();

    return (
        <>
            <div className="grid gap-2">
                <FieldLabel htmlFor="name" required>
                    {t('common.name')}
                </FieldLabel>
                <Input
                    id="name"
                    name="name"
                    required
                    autoFocus
                    placeholder={t('clients.credNamePlaceholder')}
                />
                <InputError message={errors.name} />
            </div>

            <div className="grid gap-2">
                <FieldLabel htmlFor="description">
                    {t('common.description')}
                </FieldLabel>
                <Textarea
                    id="description"
                    name="description"
                    placeholder={t('clients.descriptionPlaceholder')}
                />
                <InputError message={errors.description} />
            </div>

            <EncryptedFieldsFrame>
                <div className="grid gap-2">
                    <FieldLabel htmlFor="url">{t('common.url')}</FieldLabel>
                    <Input
                        id="url"
                        name="url"
                        type="url"
                        placeholder={t('clients.urlPlaceholder')}
                    />
                    <InputError message={errors.url} />
                </div>

                <div className="grid gap-2">
                    <FieldLabel htmlFor="login">{t('common.login')}</FieldLabel>
                    <Input id="login" name="login" autoComplete="off" />
                    <InputError message={errors.login} />
                </div>

                <div className="grid gap-2">
                    <FieldLabel htmlFor="password">{t('common.password')}</FieldLabel>
                    <PasswordInput
                        id="password"
                        name="password"
                        autoComplete="new-password"
                    />
                    <InputError message={errors.password} />
                </div>

                <div className="grid gap-2">
                    <FieldLabel htmlFor="additional_information">
                        {t('common.additionalInformation')}
                    </FieldLabel>
                    <Textarea
                        id="additional_information"
                        name="additional_information"
                        placeholder={t('clients.additionalInformationPlaceholder')}
                    />
                    <InputError message={errors.additional_information} />
                </div>
            </EncryptedFieldsFrame>

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
