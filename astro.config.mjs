import { defineConfig } from 'astro/config';
import mermaid from 'astro-mermaid';
import starlight from '@astrojs/starlight';

export default defineConfig({
  site: 'https://mindtwo.github.io',
  base: '/laravel-starter-kit',
  srcDir: './docs/src',
  publicDir: './docs/public',
  outDir: './docs/dist',
  integrations: [
    mermaid({
      theme: 'forest',
    }),
    starlight({
      title: 'Laravel Starter Kit',
      social: [
        {
          icon: 'github',
          label: 'GitHub',
          href: 'https://github.com/mindtwo/laravel-starter-kit',
        },
      ],
      sidebar: [
        {
          label: 'Guides',
          autogenerate: { directory: 'guides' },
        },
        {
          label: 'Explanation',
          autogenerate: { directory: 'explanation' },
        },
        {
          label: 'Reference',
          items: [
            { label: 'Bruno Collection', link: '/reference/bruno-collection/' },
            { label: 'API Reference', link: '/api/#/' },
          ],
        },
      ],
    }),
  ],
});
