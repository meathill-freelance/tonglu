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
const copy = (from, to) => {
  const read = fs.createReadStream(from);
  const write = fs.createWriteStream(to);
  return new Promise((resolve, reject) => {
    read.on('error', reject);
    write.on('error', reject);
    write.on('finish', resolve);
    read.pipe(write);
  })
    .catch(error => {
      read.destroy();
      write.end();
      throw error;
    });
};

export {
  copy,
  exists,
  readDir,
  readFile,
  writeFile,
  mkdir,
};
