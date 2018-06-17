import fs from 'fs';
import mkdirp from 'mkdirp';
import {promisify} from 'util';

const exists = fs.existsSync;
const readDir = (dir, filter) => {
  return new Promise((resolve, reject) => {
    fs.readdir(dir, 'utf8', (err, files) => {
      if (err) {
        return reject(err);
      }

      if (filter) {
        files = files.filter(file => filter.test(file));
      }
      return resolve(files);
    });
  });
}
const readFile = promisify(fs.readFile);
const writeFile = promisify(fs.writeFile);
const mkdir = promisify(mkdirp);

export {
  exists,
  readDir,
  readFile,
  writeFile,
  mkdir,
};
