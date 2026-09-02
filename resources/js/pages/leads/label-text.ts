import type { TFunction } from 'i18next';

export function leadLabelText(t: TFunction, label: string): string {
    return t(`leads.labels.${label}`, { defaultValue: label });
}
