/**
 * This script merges the coverage reports from Cypress and Jest into a single one,
 * inside the "coverage" folder
 */

const { execSync } = require('child_process');
// eslint-disable-next-line import/no-extraneous-dependencies
const fs = require('fs-extra');

// set the output directories
const REPORTS_FOLDER = 'coverage/reports';
const FINAL_OUTPUT_FOLDER = 'coverage/final';

/**
 * run a command in the terminal
 *
 * @param   {string}  commands  the command to run
 * @return  {void}
 */
const run = (commands) => {
  commands.forEach((command) => execSync(command, { stdio: 'inherit' }));
};

/**
 * move the coverage reports to the correct output directory
 *
 * @param   {string}  source          the path to the coverage report to move
 * @param   {string}  outputFileName  the name of the file to create in the reports folder
 * @return  {void}
 * @throws  {Error}                   if the source file does not exist, an empty file is created
 *                                    otherwise an error is thrown
 */
const move = (source, outputFileName) => {
  const output = `${REPORTS_FOLDER}/${outputFileName}`;
  try {
    fs.copyFileSync(source, output);
  } catch (err) {
    if (err.code === 'ENOENT') {
      // write an empty coverage file
      fs.writeFile(output,'{}');
    } else {
      throw err;
    }
  }
}

// Create the reports folder and move the reports from cypress and jest inside it
fs.emptyDirSync(REPORTS_FOLDER);
move('coverage/cypress/coverage-final.json', 'from-cypress.json');
move('coverage/jest/coverage-final.json', 'from-jest.json');

fs.emptyDirSync('.nyc_output');
fs.emptyDirSync(FINAL_OUTPUT_FOLDER);

// Run "nyc merge" inside the reports folder, merging the two coverage files into one,
// then generate the final report on the coverage folder
run([
  // "nyc merge" will create a "coverage.json" file on the root, we move it to .nyc_output
  `nyc merge ${REPORTS_FOLDER} && mv coverage.json .nyc_output/out.json`,
  `nyc report --reporter lcov --report-dir ${FINAL_OUTPUT_FOLDER}`,
]);
