import {exists, mkdir, writeFile} from './fs';

/**
 * 将 pug 生成的 HTML 转换成 mustache 模板
 * `<!--{{key}}-->` => `{{key}}`
 * `____class` => `{{class}}
 *
 * @param {String} html
 * @param {String} file
 * @returns {string}
 */
export default async function (html, file) {
  html = html.replace(/<\!--(\{\{\w+\}\})-->/g, (match, key) => key);
  html = html.replace(/\b_{4}(\w+)\b/g, (match, key) => `{{${key}}}`);

  const path = file.substr(0, file.lastIndexOf('/') + 1);
  if (!exists(path)) {
    await mkdir(path);
  }
  return writeFile(file, html);
}
