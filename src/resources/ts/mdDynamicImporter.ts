const mdDynamicImporter = async (
  mdPath = '',
  mdName = '',
  mdPrefixPath = 'ui-components',
) => await import(`./assets/markdown/${mdPrefixPath}/${mdPath}/${mdName}.md`);

export default mdDynamicImporter;
