export function useTextTruncate() {
    
  const truncate = (text, length) => {
    if (!text || typeof text !== 'string') return '';
    if (text.length <= length) return text;
    return text.slice(0, length).trimEnd() + '...';
  };

  return { truncate };
}
