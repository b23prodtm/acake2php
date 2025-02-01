/### ARM BEGIN/,/### ARM END/s/^(# )+(.*)/\2/g
s/(# )+(RUN [^a-z]*cross-build-start[^a-z]*)/\2/g
s/(# )+(RUN [^a-z]*cross-build-end[^a-z]*)/\2/g
