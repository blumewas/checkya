declare global {
  interface Window {
    COOKIE_CONSENT_ENABLED: boolean | undefined;
    COOKIE_DOMAINS: string[] | undefined;
    CONSENT_DOMAIN: string | undefined;
    GTM_ID: string;
  }
}

export interface Resource {
  id: number;
}
