#!/usr/bin/env node

const {resolve} = require('path');
const {readFile, readdir} = require('fs/promises');
const html2tpl = require('../helper/html2tpl');

(async () => {
  const inDir = resolve(__dirname, '../html');
  const outDir = resolve(__dirname, '../../template');
  const content = await readFile(`${inDir}/homepage.html`, 'utf8');
  let [header, body, footer] = content.split('<!--split-->');
  await html2tpl(header, `${outDir}/header.html`);
  await html2tpl(footer, `${outDir}/footer.html`);
  const files = await readdir(inDir);
  await Promise.all(files.map(async file => {
    if (!/\.html$/.test(file)) return;

    const content = await readFile(`${inDir}/${file}`, 'utf8');
    const parts = content.split('<!--split-->');
    await html2tpl(parts.length > 1 ? parts[1] : content, `${outDir}/${file}`);
  }));

  console.log('template files generated');
})();
